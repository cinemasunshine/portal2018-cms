# Webサーバ用Dockerfile

# https://hub.docker.com/_/php/
FROM php:7.2-apache

# mod_rewrite
RUN a2enmod rewrite

# DocumentRoot変更
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# apt-getをupdate（installできなかったので）
# install
#    - git        composerのため
#    - zip、unzip composerのエラー解決
#    - libicu-dev intl PHP extensionのため
#    - libfreetype6-dev    GD PHP extensionのため
#    - libjpeg62-turbo-dev GD PHP extensionのため
#    - libpng-dev          GD PHP extensionのため
RUN apt-get update \
    && apt-get install -y \
        git \
        zip \
        unzip \
        libicu-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# install PHP extensions
RUN docker-php-ext-configure gd \
        --with-freetype-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/
        
RUN docker-php-ext-install \
        pdo_mysql \
        intl \
        -j$(nproc) gd

# composerインストール
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# サーバ証明書に関する一時的な対応
RUN git config --global http.sslverify false

COPY php.ini /usr/local/etc/php
