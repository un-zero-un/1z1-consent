DOCKER_COMPOSE = EXTERNAL_USER_ID=$(shell id -u) docker compose
HTTPS_PORT ?= 443

.PHONY: ps build up first_run clean logs cli run reset cc deploy down test hadolint psalm psalm_strict

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

run: .configured up ## Automatically Build & Run the project

clean: ## Stops and clean up the project (removes all data)
	@$(DOCKER_COMPOSE) down -v --remove-orphans
	@rm -rf .configured vendor node_modules var/cache var/log infra/docker/php/tls public/build public/bundles

ps: ## Show running containers
	@$(DOCKER_COMPOSE) ps

pull: ## Pulls remote images
	@$(DOCKER_COMPOSE) pull --ignore-pull-failures

build: ## Build the project
	@$(DOCKER_COMPOSE) build

up: ## Start the containers
	@$(DOCKER_COMPOSE) up -d --remove-orphans --wait php

down: ## Stop the containers
	@$(DOCKER_COMPOSE) down --remove-orphans

cli: ## Open a shell in the php container
	@$(DOCKER_COMPOSE) exec php bash

.configured:
	@test -f $@ || make first_run
	@touch $@

first_run: php/tls/cert.pem pull build vendor/ up node_modules/ public/build/ reset

reset: env=dev
reset: ## Reset project fixtures
	$(DOCKER_COMPOSE) exec -e$(env) php composer reset

cc: env=dev
cc: ## Clear the Symfony cache
	@$(DOCKER_COMPOSE) exec -e$(env) php bin/console cache:clear

logs: ## Show logs, use "c=" to specify a container, default is php
	@$(eval c ?= 'php')
	@$(eval tail ?= 100)
	@$(DOCKER_COMPOSE) logs $(c) --tail=$(tail) --follow

vendor/:
	@$(DOCKER_COMPOSE) run --rm php composer install

node_modules/: package.json yarn.lock
	@$(DOCKER_COMPOSE) run --rm php yarn install

public/build/:
	@$(DOCKER_COMPOSE) run --rm php yarn dev

php/tls/cert.pem:
	@mkdir -p infra/docker/php/tls
	@mkcert -key-file infra/docker/php/tls/key.pem -cert-file infra/docker/php/tls/cert.pem localhost www.localhost

test: env=test
test: ## Run PHPUnit test suite
	@$(DOCKER_COMPOSE) exec -eAPP_ENV=$(env) php composer reset-test
	@$(DOCKER_COMPOSE) exec -eAPP_ENV=$(env) php ./vendor/bin/phpunit

test-coverage: env=test
test-coverage: reset ## Run PHPUnit test suite with HTML code coverage
	@$(DOCKER_COMPOSE) exec -eAPP_ENV=$(env) -eXDEBUG_MODE=coverage php ./vendor/bin/phpunit --coverage-html=public/coverage

infection:
	@$(DOCKER_COMPOSE) exec php php -dmemory_limit=-1 ./vendor/bin/infection --threads=4 --logger-html=public/infection

hadolint: ## Link Dockerfile
	@docker pull hadolint/hadolint
	@docker run --rm -i hadolint/hadolint hadolint - < Dockerfile

cs: ## Fix code style
	@docker run --rm -v $(PWD):/app -w /app ghcr.io/php-cs-fixer/php-cs-fixer:3-php8.4 fix src
	@docker run --rm -v $(PWD):/app -w /app ghcr.io/php-cs-fixer/php-cs-fixer:3-php8.4 fix tests
	@docker compose exec -T php ./vendor/bin/twig-cs-fixer fix templates

psalm: ## Run static analysis
	@$(DOCKER_COMPOSE) exec php ./vendor/bin/psalm --no-diff

psalm_strict: ## Run static analysis (strict mode)
	@$(DOCKER_COMPOSE) exec php ./vendor/bin/psalm --show-info=true --no-diff
