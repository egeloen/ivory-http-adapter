# Installation

To install the Ivory http adapter library, you will need [Composer](http://getcomposer.org). It's a PHP 5.3+
dependency manager which allows you to declare the dependent libraries your project needs and it will install &
autoload them for you.

## Set up Composer

Composer comes with a simple phar file. To easily access it from anywhere on your system, you can execute:

```
$ curl -s https://getcomposer.org/installer | php
$ sudo mv composer.phar /usr/local/bin/composer
```

## Define dependencies

Create a ``composer.json`` file at the root directory of your project and simply require the
``egeloen/http-adapter`` package:

```
{
    "require": {
        "egeloen/http-adapter": "*"
    }
}
```

Obviously, if you want to use an adapter which requires an extra package, you will need to require it too. See the
[composer.json](/composer.json) for the allowed packages.

## Install dependencies

Now, you have define your dependencies, you can install them:

```
$ composer install
```

Composer will automatically download your dependencies & create an autoload file in the ``vendor`` directory.

## Autoload

So easy, you just have to require the generated autoload file and you are already ready to play:

``` php
require __DIR__.'/vendor/autoload.php';

use Ivory\HttpAdapter;

// ...
```

The Ivory Http Adapter library follows the [PSR-4 Standard](http://www.php-fig.org/psr/psr-4/). If you prefer install
it manually, it can be autoload by any convenient autoloader.
