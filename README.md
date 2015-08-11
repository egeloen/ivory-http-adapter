# README

[![Build Status](https://secure.travis-ci.org/egeloen/ivory-http-adapter.png?branch=master)](http://travis-ci.org/egeloen/ivory-http-adapter)
[![Coverage Status](https://coveralls.io/repos/egeloen/ivory-http-adapter/badge.png?branch=master)](https://coveralls.io/r/egeloen/ivory-http-adapter?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/egeloen/ivory-http-adapter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/egeloen/ivory-http-adapter/?branch=master)
[![Dependency Status](http://www.versioneye.com/php/egeloen:http-adapter/badge.svg)](http://www.versioneye.com/php/egeloen:http-adapter)

[![Latest Stable Version](https://poser.pugx.org/egeloen/http-adapter/v/stable.svg)](https://packagist.org/packages/egeloen/http-adapter)
[![Latest Unstable Version](https://poser.pugx.org/egeloen/http-adapter/v/unstable.svg)](https://packagist.org/packages/egeloen/http-adapter)
[![Total Downloads](https://poser.pugx.org/egeloen/http-adapter/downloads.svg)](https://packagist.org/packages/egeloen/http-adapter)
[![License](https://poser.pugx.org/egeloen/http-adapter/license.svg)](https://packagist.org/packages/egeloen/http-adapter)

The library allows to issue HTTP requests with PHP 5.4.8+. The supported adapters are:

 - [Buzz](https://github.com/kriswallsmith/Buzz)
 - [Cake](http://cakephp.org/)
 - [cURL](http://curl.haxx.se/)
 - [FileGetContents](http://php.net/manual/en/function.file-get-contents.php)
 - [Fopen](http://php.net/manual/en/function.fopen.php)
 - [Guzzle3](http://guzzle3.readthedocs.org/)
 - [Guzzle4](http://guzzle.readthedocs.org/en/v5/)
 - [Guzzle5](http://guzzle.readthedocs.org/en/v5/)
 - [Guzzle6](http://guzzle.readthedocs.org/en/v6/)
 - [Httpful](http://phphttpclient.com/)
 - [Pecl Http](http://devel-m6w6.rhcloud.com/mdref/http)
 - [React](http://reactphp.org/)
 - [Socket](http://php.net/manual/en/function.stream-socket-client.php)
 - [Zend1](http://framework.zend.com/manual/1.12/en/zend.http.html)
 - [Zend2](http://framework.zend.com/manual/2.0/en/modules/zend.http.html)

Additionally, it follows the [PSR-7 Standard](https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md)
which defines how http message should be implemented.

## Documentation

 1. [Installation](/doc/installation.md)
 2. [Adapters](/doc/adapters.md)
 3. [Configuration](/doc/configuration.md)
 4. [PSR-7](/doc/psr-7.md)
 5. [Usage](/doc/usage.md)
 6. [Events](/doc/events.md)

## Cookbook

 - [Log requests, responses and exceptions](/doc/events.md#logger)
 - [Journalize requests and responses](/doc/events.md#history)
 - [Throw exceptions for errored responses](/doc/events.md#status-code)
 - [Retry errored requests](/doc/events.md#retry)
 - [Follow redirects](/doc/events.md#redirect)
 - [Manage cookies](/doc/events.md#cookie)

## Testing

The library is fully unit tested by [PHPUnit](http://www.phpunit.de/) with a code coverage close to **100%**. To
execute the test suite, check the travis [configuration](/.travis.yml).

## Contribute

We love contributors! The library is open source, if you'd like to contribute, feel free to propose a PR!

## License

The Ivory Http Adapter is under the MIT license. For the full copyright and license information, please read the
[LICENSE](/LICENSE) file that was distributed with this source code.
