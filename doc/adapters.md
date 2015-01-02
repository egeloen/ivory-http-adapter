# Adapters

In order to make an http request, you need to create an adapter. An adapter is designed around the
`Ivory\HttpAdapter\HttpAdapterInterface` and represents the central point of the library.

## Buzz

``` php
use Buzz\Browser;
use Ivory\HttpAdapter\BuzzHttpAdapter;

$httpAdapter = new BuzzHttpAdapter();
// or
$httpAdapter = new BuzzHttpAdapter(new Browser());
```

## Cake

``` php
use Ivory\HttpAdapter\CakeHttpAdapter;

$httpAdapter = new CakeHttpAdapter();
// or
$httpAdapter = new CakeHttpAdapter(new \HttpSocket());
```

## cURL

``` php
use Ivory\HttpAdapter\CurlHttpAdapter;

$httpAdapter = new CurlHttpAdapter();
```

## File get contents

``` php
use Ivory\HttpAdapter\FileGetContentsHttpAdapter;

$httpAdapter = new FileGetContentsHttpAdapter();
```

## Fopen

``` php
use Ivory\HttpAdapter\FopenHttpAdapter;

$httpAdapter = new FopenHttpAdapter();
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

## React

``` php
use Ivory\HttpAdapter\ReactHttpAdapter;

$reactHttpAdapter = new ReactHttpAdapter();
```

The React http adapter does not support all features. The limitations are:

 * HTTP 1.1 not supported.
 * Timeout not suppoted.

## Socket

``` php
use Ivory\HttpAdapter\SocketHttpAdapter;

$httpAdapter = new SocketHttpAdapter();
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

## Stopwatch

The stopwatch http adapter allows you to time the http adaper process (including subscribers, etc) through the Symfony2
stopwatch component.

``` php
use Ivory\HttpAdapter\CurlHttpAdapter;
use Ivory\HttpAdapter\SocketHttpAdapter;
use Ivory\HttpAdapter\StopwatchHttpAdapter;
use Symfony\Component\Stopwatch\Stopwatch;

$httpAdapter = new CurlHttpAdapter();
// or
$httpAdapter = new SocketHttpAdpater();

$stopwatch = new Stopwatch();

$stopwatchHttpAdapter = new StopwatchHttpAdapter($httpAdapter, $stopwatch);
```

## Factory

You can either construct your http adapter through a factory. For example, in order to create a curl http adapter, you
can do:

``` php
use Ivory\HttpAdapter\HttpAdapterFactory;

$httpAdapter = HttpAdapterFactory::create('curl');
```

The available adapters are: `buzz`, `cake`, `curl`, `file_get_contents`, `fopen`, `guzzle`, `guzzle_http`, `httpful`,
`react`, `socket`, `zend1` or `zend2`.

If you want to know if an adapter is available on your system, you can use:

``` php
use Ivory\HttpAdapter\HttpAdapterFactory;

$httpAdapter = HttpAdapterFactory::capable(HttpAdapterFactory::BUZZ);
```

If you are not aware of the available adapters and just want to pick one, you can use:

``` php
use Ivory\HttpAdapter\HttpAdapterFactory;

$httpAdapter = HttpAdapterFactory::guess();

// or with a specific preference
$httpAdapter = HttpAdapterFactory::guess(HttpAdapterFactory::BUZZ);

// or with multiple preferences
$httpAdapter = HttpAdapterFactory::guess(array(
    HttpAdapterFactory::BUZZ,
    HttpAdapterFactory::HTTPFUL,
));
```

You can additionally register your own http adapters:

``` php
use Ivory\HttpAdapter\HttpAdapterFactory;

HttpAdapterFactory::register('my_http_adapter', 'My\Own\HttpAdapter');
$httpAdapter = HttpAdapterFactory::create('my_http_adapter');
```

The `register` method takes a third optional parameters which represents the client used. It is used internally in
order to determine if the adapters is available. It can be either a class name, a function name or an ini option. If
you don't provide it, we consider your adapter as available.
