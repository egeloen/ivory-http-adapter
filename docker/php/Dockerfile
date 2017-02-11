FROM php:latest

# APT packages
RUN apt-get update && apt-get install -y \
    libcurl4-nss-dev \
    libicu-dev \
    zlib1g-dev \
    git \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install intl zip

# Propro & Raphf extensions must be installed/loaded before compiling pecl-http
RUN pecl install propro raphf \
    && docker-php-ext-enable propro raphf \
    && rm -rf /tmp/pear

# Pecl extensions
RUN pecl install pecl_http xdebug \
    && docker-php-ext-enable http xdebug \
    && rm -rf /tmp/pear

# XDebug configuration
COPY config/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/usr/local/bin

# Bash
RUN chsh -s /bin/bash www-data

# Workdir
WORKDIR /var/www/html

# Entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
