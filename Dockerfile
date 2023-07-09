FROM php:8.0.2-fpm

ARG user=appuser
ARG group=appuser
ARG uid=1000
ARG gid=1000

RUN groupadd -g ${gid} ${group}
RUN useradd -u ${uid} -g ${group} -s /bin/sh -m ${user}

RUN apt-get update -y
RUN apt-get install -y
RUN apt-get install -y \
    libzip-dev unzip libonig-dev libpng-dev \
    libwebp-dev libjpeg62-turbo-dev libxpm-dev \
    libfreetype6-dev libpq-dev
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql zip mbstring 

# config and install php gd
RUN docker-php-ext-configure gd \
    --with-jpeg \
    --with-freetype
RUN docker-php-ext-install gd

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# RUN chmod -R 777 storage
USER ${uid}:${gid}
