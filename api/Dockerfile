FROM php:8.0-fpm-alpine AS api_php

RUN apk add --no-cache \
		acl \
		file \
		gettext \
		git \
	;

ARG APCU_VERSION=5.1.18
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		libzip-dev \
		libxml2-dev \
		postgresql-dev \
		zlib-dev \
	; \
	\
	docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
		intl \
		pdo_pgsql \
		zip \
		soap \
	;

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY docker/php/php.dev.ini $PHP_INI_DIR/conf.d/api-platform.ini

# Workaround to allow using PHPUnit 8 with Symfony 4.3
ENV SYMFONY_PHPUNIT_VERSION=8.3

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
# install Symfony Flex globally to speed up download of Composer packages (parallelized prefetching)
RUN set -eux; \
	composer global require "symfony/flex" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
	composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /srv/api

# build for production
ARG APP_ENV=prod

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.json composer.lock symfony.lock ./
RUN set -eux; \
	composer install --prefer-dist --no-scripts --no-progress --no-suggest; \
	composer clear-cache

# do not use .env files in production
COPY .env .env.test ./
#RUN composer dump-env prod; \
#	rm .env

# copy only specifically what we need
COPY bin bin/
COPY config config/
COPY public public/
COPY src src/
COPY tests tests/

COPY phpunit.xml.dist ./

RUN set -eux; \
	mkdir -p var/cache var/log; \
    chmod 777 -R var/; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync
VOLUME /srv/api/var

CMD ["php-fpm"]


FROM nginx:stable-alpine AS api_nginx

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /srv/api/public

COPY --from=api_php /srv/api/public ./