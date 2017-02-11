#!/usr/bin/env bash

set -e

DOCKER_BUILD=${DOCKER_BUILD-false}

if [ "$DOCKER_BUILD" = false ]; then
    sed -i "/^disable_functions =.*$/d" ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini
    wget https://scrutinizer-ci.com/ocular.phar
    php ocular.phar code-coverage:upload --format=php-clover build/clover.xml
fi
