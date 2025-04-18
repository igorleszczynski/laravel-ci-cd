FROM node:22 AS yarn

WORKDIR /var/www/html
COPY . ./
RUN set -ex; \
    \
    yarn install --immutable; \
    yarn run build

FROM php:8.4-cli-alpine

WORKDIR /var/www/html
ARG WWWGROUP
ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apk update
RUN apk add --no-cache \
        bash \
        curl \
        gnupg \
        zip \
        unzip \
        supervisor \
        libpng-dev \
        bind-tools \
        librsvg \
        ffmpeg \
        nano \
        mysql-client \
        libcap \
        nodejs \
        npm \
        yarn \
        icu-dev \
        linux-headers \
        autoconf \
        gcc \
        g++ \
        make \
        brotli \
        brotli-dev \
        brotli-libs \
        imagemagick \
        imagemagick-dev \
        pkgconfig
RUN docker-php-ext-install pdo_mysql gd bcmath intl sockets pcntl
RUN pecl install redis swoole imagick
RUN docker-php-ext-enable redis swoole imagick
RUN npm install -g pnpm

RUN apk del --no-cache gnupg \
    && rm -rf /var/cache/apk/*

RUN setcap "cap_net_bind_service=+ep" $(which php)

COPY --chown=www-data:www-data . ./

RUN set -ex; \
    \
    mkdir -p bootstrap/cache; \
    mkdir -p storage; \
    chown -R www-data:www-data bootstrap/cache storage; \
    chmod -R g+w bootstrap/cache storage

# Composer installation
COPY scripts/docker/install-composer.sh /usr/local/sbin/
RUN chmod +x /usr/local/sbin/install-composer.sh && install-composer.sh

# Install composer dependencies
RUN set -ex; \
    \
    mkdir -p storage/framework/views; \
    composer install --no-progress --no-interaction --prefer-dist --optimize-autoloader --no-dev; \
    composer clear-cache; \
    rm -rf .composer

# Install assets
COPY --from=yarn --chown=www-data:www-data /var/www/html/public/build ./public/build

COPY scripts/docker/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]
EXPOSE 8000
