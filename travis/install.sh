#!/usr/bin/env bash

set -e

TRAVIS_PHP_VERSION=${TRAVIS_PHP_VERSION-5.6}
ENABLE_CURL=${ENABLE_CURL-false}
GUZZLE_VERSION=${GUZZLE_VERSION-6.*}
COMPOSER_PREFER_LOWEST=${COMPOSER_PREFER_LOWEST-false}
DOCKER_BUILD=${DOCKER_BUILD-false}

if [ "$DOCKER_BUILD" = true ]; then
    cp .env.dist .env

    docker-compose build
    docker-compose run --rm php composer update --prefer-source

    exit
fi

if [[ "$TRAVIS_PHP_VERSION" =~ ^5.* ]]; then
    printf "\n" | pecl install propro-1.0.2
    printf "\n" | pecl install raphf-1.1.2
    printf "\n" | pecl install pecl_http-2.5.6
fi

if [[ "$TRAVIS_PHP_VERSION" =~ ^7.* ]]; then
    printf "\n" | pecl install propro
    printf "\n" | pecl install raphf
    printf "\n" | pecl install pecl_http
fi

if [ "$ENABLE_CURL" = false ]; then
    mkdir -p ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d
    echo "disable_functions = curl_init" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini
fi

if [ "$TRAVIS_PHP_VERSION" = "5.6" ]; then
    echo "always_populate_raw_post_data = -1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini
fi

php -S 127.0.0.1:8080 -t tests/Fixtures > /dev/null 2>&1 &

if [ ! "$GUZZLE_VERSION" = "6.*" ]; then
    composer require --no-update --dev guzzlehttp/guzzle:${GUZZLE_VERSION}
fi

composer remove --no-update --dev friendsofphp/php-cs-fixer

composer update --prefer-source `if [ "$COMPOSER_PREFER_LOWEST" = true ]; then echo "--prefer-lowest --prefer-stable"; fi`
