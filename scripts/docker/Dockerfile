# Etap 1 – Frontend
FROM node:22-alpine AS frontend

WORKDIR /app

COPY resources resources
COPY vite.config.ts package.json tsconfig.json ./

RUN npm install -g pnpm && pnpm install && pnpm run build

# Etap 2 – Backend
FROM php:8.4-cli-alpine

WORKDIR /var/www/html
ARG WWWGROUP
ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apk update && \
    apk add --no-cache bash curl zip unzip supervisor libpng-dev librsvg ffmpeg mysql-client libcap icu-dev linux-headers autoconf gcc g++ make brotli brotli-dev brotli-libs imagemagick imagemagick-dev pkgconfig && \
    docker-php-ext-install pdo_mysql gd bcmath intl sockets pcntl && \
    pecl install redis swoole imagick && \
    docker-php-ext-enable redis swoole imagick && \
    rm -rf /var/cache/apk/*

RUN setcap "cap_net_bind_service=+ep" $(which php)

COPY --chown=www-data:www-data . ./

RUN set -ex; \
    mkdir -p bootstrap/cache; \
    mkdir -p storage; \
    chown -R www-data:www-data bootstrap/cache storage; \
    chmod -R g+w bootstrap/cache storage

# Composer installation
COPY scripts/docker/install-composer.sh /usr/local/sbin/
RUN chmod +x /usr/local/sbin/install-composer.sh && install-composer.sh

# Install composer dependencies
RUN set -ex; \
    mkdir -p storage/framework/views; \
    composer install --no-progress --no-interaction --prefer-dist --optimize-autoloader --no-dev; \
    composer clear-cache; \
    rm -rf .composer

# Install assets
COPY --from=frontend --chown=www-data:www-data /app/public/build ./public/build

COPY --chown=www-data:www-data .env.production .env

RUN rm -rf \
    .env.production \
    .prettierignore \
    .prettierrc \
    .eslint.config.js \
    scripts/realpath.sh \
    scripts/docker/nginx \
    scripts/docker/Dockerfile \
    scripts/docker/install-composer.sh \
    tsconfig.json

COPY scripts/docker/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]
EXPOSE 8000
