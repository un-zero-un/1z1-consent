#syntax=docker/dockerfile:1

ARG FRANKENPHP_VERSION=1.10
ARG PHP_VERSION=8.5
ARG NODE_VERSION=24
ARG DEBIAN_VERSION=trixie

FROM node:${NODE_VERSION}-${DEBIAN_VERSION} AS node_base

FROM dunglas/frankenphp:${FRANKENPHP_VERSION}-php${PHP_VERSION}-${DEBIAN_VERSION} AS base

LABEL org.opencontainers.image.source=https://github.com/un-zero-un/1z1-consent
LABEL org.opencontainers.image.licenses=MIT
LABEL org.opencontainers.image.authors="Yohan Giarelli <yohan@giarel.li>"
LABEL org.opencontainers.image.description="1z1 Consent is a GDPR-compliant cookie consent management platform for modern web applications."

ARG EXTERNAL_USER_ID

# persistent / runtime deps
# hadolint ignore=DL3008
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends git unzip ca-certificates sqlite3; \
    php -v; \
    install-php-extensions zip pdo_pgsql pdo_mysql pcntl opcache intl apcu memcached redis; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*; \
    mkdir -p /app; \
    sync

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN set -eux; \
    sed -i -r s/"(www-data:x:)([[:digit:]]+):([[:digit:]]+):"/\\1${EXTERNAL_USER_ID}:${EXTERNAL_USER_ID}:/g /etc/passwd; \
    sed -i -r s/"(www-data:x:)([[:digit:]]+):"/\\1${EXTERNAL_USER_ID}:/g /etc/group; \
    mkdir -p /var/run/php /data /config /app/var/cache /app/var/log /app/var/data; \
    chown -R www-data:www-data /app /var/www /usr/local/etc/php /var/run/php /data /config /app/var/cache /app/var/log /app/var/data

VOLUME /config
VOLUME /data
VOLUME /app/var/log
VOLUME /app/var/cache

COPY --chown=www-data:www-data infra/docker/php/Caddyfile /etc/caddy/Caddyfile
COPY --chown=www-data:www-data infra/docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

ARG STAGE=dev

RUN ln -s "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY infra/docker/php/conf.d/symfony.prod.ini $PHP_INI_DIR/conf.d/symfony.ini

ARG APP_ENV=prod
ARG APP_DEBUG=false

WORKDIR /app

COPY --chown=node:node composer.json composer.lock symfony.lock ./
RUN set -eux; \
    composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress; \
    composer clear-cache


FROM node_base AS node

ARG EXTERNAL_USER_ID

RUN set -eux; \
    mkdir -p /app; \
    chown -R node:node /app

RUN set -eux; \
    sed -i -r s/"(node:x:)([[:digit:]]+):([[:digit:]]+):"/\\1${EXTERNAL_USER_ID}:${EXTERNAL_USER_ID}:/g /etc/passwd; \
    sed -i -r s/"(node:x:)([[:digit:]]+):"/\\1${EXTERNAL_USER_ID}:/g /etc/group; \
    chown -R node:node /app /home/node

USER node
WORKDIR /app

COPY --chown=node:node package.json yarn.lock webpack.config.js tsconfig.json ./
COPY --chown=node:node --from=base /app/vendor vendor/

RUN yarn

COPY --chown=node:node assets assets/
COPY --chown=node:node --from=base /app/public public/

RUN yarn build




FROM base AS php

ARG MAIN_DOMAIN
ARG APP_ENV=prod

USER root

RUN set -eux; \
    chown -R www-data:www-data /app; \
    sync

USER www-data
WORKDIR /app

COPY --chown=www-data:www-data .env ./
COPY --chown=www-data:www-data assets assets/
COPY --chown=www-data:www-data bin bin/
COPY --chown=www-data:www-data config config/
COPY --chown=www-data:www-data migrations migrations/
COPY --chown=www-data:www-data --from=node /app/public public/
COPY --chown=www-data:www-data public public/
COPY --chown=www-data:www-data src src/
COPY --chown=www-data:www-data templates templates/
COPY --chown=www-data:www-data translations translations/

RUN set -eux; \
    mkdir -p var/cache var/log; \
    composer install --prefer-dist --no-dev --no-progress; \
    composer dump-autoload --optimize --no-dev --classmap-authoritative; \
    php bin/console cache:clear; \
    php bin/console cache:warmup -eprod; \
    chmod +x bin/console; \
    sync

HEALTHCHECK CMD curl -f http://localhost:2019/metrics || exit 1

CMD [ "frankenphp", "run", "--config", "/etc/caddy/Caddyfile" ]

EXPOSE 80
