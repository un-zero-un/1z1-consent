#syntax=docker/dockerfile:1

ARG FRANKENPHP_VERSION=1.9
ARG PHP_VERSION=8.4
ARG NODE_VERSION=22
ARG DEBIAN_VERSION=trixie

FROM node:${NODE_VERSION}-${DEBIAN_VERSION} AS node_base

FROM dunglas/frankenphp:${FRANKENPHP_VERSION}-php${PHP_VERSION}-${DEBIAN_VERSION} AS base

LABEL org.opencontainers.image.source=https://github.com/un-zero-un/1z1-consent
LABEL org.opencontainers.image.licenses=MIT
LABEL org.opencontainers.image.authors="Yohan Giarelli <yohan@giarel.li>"
LABEL org.opencontainers.image.description="1z1 Consent is a GDPR-compliant cookie consent management platform for modern web applications."

ARG EXTERNAL_USER_ID

VOLUME /var/cache/apt
VOLUME /var/www/.cache

# persistent / runtime deps
# hadolint ignore=DL3008
RUN --mount=type=cache,target=/var/cache/apt \
    set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends libnss3-tools git acl unzip ca-certificates; \
    php -v; \
    install-php-extensions zip pdo_pgsql pcntl opcache intl apcu redis; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*; \
    mkdir -p /app; \
    sync

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN set -eux; \
    sed -i -r s/"(www-data:x:)([[:digit:]]+):([[:digit:]]+):"/\\1${EXTERNAL_USER_ID}:${EXTERNAL_USER_ID}:/g /etc/passwd; \
    sed -i -r s/"(www-data:x:)([[:digit:]]+):"/\\1${EXTERNAL_USER_ID}:/g /etc/group; \
    mkdir -p /var/run/php /data/caddy /config/caddy /app/var/cache /app/var/data; \
    chown -R www-data:www-data /app /var/www /usr/local/etc/php /var/run/php /data/caddy /config/caddy /app/var/cache /app/var/data

VOLUME /config
VOLUME /data
VOLUME /app/var/cache
VOLUME /app/var/data

COPY --chown=www-data:www-data infra/docker/php/Caddyfile /etc/caddy/Caddyfile
COPY --chown=www-data:www-data infra/docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

ARG STAGE=dev

RUN ln -s "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY infra/docker/php/conf.d/symfony.prod.ini $PHP_INI_DIR/conf.d/symfony.ini

ARG APP_ENV=prod
ARG APP_DEBUG=false

WORKDIR /app

COPY --chown=www-data:www-data composer.json composer.lock symfony.lock ./
RUN --mount=type=cache,target=/var/www/.cache \
    set -eux; \
    composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress; \
    composer clear-cache; \
    mkdir -p var

COPY --from=node_base /usr/local/bin/node /usr/local/bin/node
COPY --from=node_base /usr/local/include/node /usr/local/include/node
COPY --from=node_base /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node_base /opt/yarn* /opt/yarn

RUN set -eux; \
    ln -vs /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm; \
    ln -vs /opt/yarn/bin/yarn /usr/local/bin/yarn


FROM base AS node

RUN set -eux; \
    mkdir -p /app; \
    chown -R www-data:www-data /app

USER www-data
WORKDIR /app

COPY --chown=www-data:www-data package.json yarn.lock webpack.config.js tsconfig.json ./
RUN set -eux; \
    yarn; \
    sync

COPY --chown=www-data:www-data assets assets/
COPY --chown=www-data:www-data --from=base /app/public public/
COPY --chown=www-data:www-data --from=base /app/vendor vendor/
RUN set -eux; \
    yarn build; \
    sync





FROM base AS php

ARG MAIN_DOMAIN
ARG APP_ENV=prod
ARG APP_DEBUG=false

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
COPY --chown=www-data:www-data public/index.php public/prod.php public/
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

EXPOSE 80
