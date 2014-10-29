# Adapters

In order to make an http request, you need to create an adapter. An adapter is designed around the
`Ivory\HttpAdapter\HttpAdapterInterface` and represents the central point of the library.

## cURL

``` php
use Ivory\HttpAdapter\CurlHttpAdapter;

$httpAdapter = new CurlHttpAdapter();
```

## Fopen

``` php
use Ivory\HttpAdapter\FopenHttpAdapter;

$httpAdapter = new FopenHttpAdapter();
```

## File get contents

``` php
use Ivory\HttpAdapter\FileGetContentsHttpAdapter;

$httpAdapter = new FileGetContentsHttpAdapter();
```

## Socket

``` php
use Ivory\HttpAdapter\SocketHttpAdapter;

$httpAdapter = new SocketHttpAdapter();
```

## Buzz

``` php
use Buzz\Browser;
use Ivory\HttpAdapter\BuzzHttpAdapter;

$httpAdapter = new BuzzHttpAdapter();
// or
$httpAdapter = new BuzzHttpAdapter(new Browser());
```

## Guzzle

``` php
use Guzzle\Http\Client;
use Ivory\HttpAdapter\GuzzleHttpAdapter;

$httpAdapter = new GuzzleHttpAdapter();
// or
$httpAdapter = new GuzzleHttpAdapter(new Client());
```

## Guzzle http

``` php
use GuzzleHttp\Client;
use Ivory\HttpAdapter\GuzzleHttpHttpAdapter;

$httpAdapter = new GuzzleHttpHttpAdapter();
// or
$httpAdapter = new GuzzleHttpHttpAdapter(new Client());
```

## Httpful

``` php
use Ivory\HttpAdapter\HttpfulHttpAdapter;

$httpAdapter = new HttpfulHttpAdapter();
```

## Zend 1

``` php
use Ivory\HttpAdapter\Zend1HttpAdapter;

$zend1HttpAdapter = new Zend1HttpAdapter();
// or
$zend1HttpAdapter = new Zend1HttpAdapter(new \Zend_Http_Client());
```

## Zend 2

``` php
use Ivory\HttpAdapter\Zend2HttpAdapter;
use Zend\Http\Client;

$zend2HttpAdapter = new Zend2HttpAdapter();
// or
$zend2HttpAdapter = new Zend2HttpAdapter(new Client());
```
