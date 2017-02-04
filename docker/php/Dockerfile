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

# Propro & Raphf extensions must be loaded before compiling pecl-http
COPY config/propro.ini /usr/local/etc/php/conf.d/propro.ini
COPY config/raphf.ini /usr/local/etc/php/conf.d/raphf.ini

# Pecl extensions
RUN pecl install propro raphf pecl_http xdebug && rm -rf /tmp/pear
COPY config/* /usr/local/etc/php/conf.d/

# Bash
RUN chsh -s /bin/bash www-data

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/usr/local/bin

# Workdir
WORKDIR /var/www/html

# Entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
