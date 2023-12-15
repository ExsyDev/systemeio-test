FROM php:8-fpm

# install os base packeges
RUN apt update \
    && apt install -y \
        apt-utils \
        curl \
        zip unzip \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        icu-devtools \
        libicu-dev \
        libmcrypt4 \
        libmcrypt-dev \
        libpng-dev \
        zlib1g-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev \
        graphviz \
        libcurl4-openssl-dev \
        pkg-config \
        libldap2-dev \
        libpq-dev

# install php extensions
RUN docker-php-ext-configure intl --enable-intl && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd && \
    docker-php-ext-install bcmath && \
    docker-php-ext-install pgsql pdo pdo_pgsql \
        intl iconv mbstring \
        zip pcntl \
        exif opcache \
    && docker-php-source delete \
    && docker-php-ext-enable pdo_pgsql

# config php
COPY .docker/php-fpm/php.ini "$PHP_INI_DIR/conf.d"
COPY .docker/php-fpm/www.conf "$PHP_INI_DIR/../php-fpm.d"

# create www user
RUN useradd -ms /bin/bash www

# copy local files
WORKDIR /var/www
COPY . .

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# set permisions
RUN chown -R www:www /var/www
RUN chmod -R 777 /var/www/public

USER www

EXPOSE 9000

CMD ["php-fpm", "-F"]