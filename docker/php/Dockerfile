FROM php:7.3-fpm-alpine

# persistent / runtime deps
RUN apk add --no-cache \
		acl \
		file \
		gettext \
		git \
	;

ARG APCU_VERSION=5.1.17
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		libzip-dev \
		postgresql-dev \
		zlib-dev \
		libssh-dev \
		rabbitmq-c-dev \
	; \
	\
	docker-php-ext-configure zip --with-libzip; \
	docker-php-ext-install -j$(nproc) \
		intl \
		pdo_pgsql \
		zip \
		bcmath \
		sockets \
	; \
	pecl install \
		apcu-${APCU_VERSION} \
		amqp \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
		amqp \
	; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .api-phpexts-rundeps $runDeps; \
	\
	apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/conf.d/api-platform.ini $PHP_INI_DIR/conf.d/api-platform.ini

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
# install Symfony Flex globally to speed up download of Composer packages (parallelized prefetching)
RUN set -eux; \
	composer global require "symfony/flex" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
	composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /srv/api
COPY . ./

# build for production
ARG APP_ENV=prod

# prevent the reinstallation of vendors at every changes in the source code
#COPY composer.json composer.lock symfony.lock ./
# do not use .env files in production
RUN echo '<?php return [];' > .env.local.php
RUN set -eux; \
	composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress --no-suggest; \
	composer clear-cache

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]
