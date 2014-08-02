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

## Guzzle 3

``` php
use Guzzle\Http\Client;
use Ivory\HttpAdapter\Guzzle3HttpAdapter;

$httpAdapter = new Guzzle3HttpAdapter();
// or
$httpAdapter = new Guzzle3HttpAdapter(new Client());
```

## Guzzle 4

``` php
use GuzzleHttp\Client;
use Ivory\HttpAdapter\Guzzle4HttpAdapter;

$httpAdapter = new Guzzle4HttpAdapter();
// or
$httpAdapter = new Guzzle4HttpAdapter(new Client());
```

## Httpful

``` php
use Ivory\HttpAdapter\HttpfulHttpAdapter;

$httpAdapter = new HttpfulHttpAdapter();
```

## Zend 2

``` php
use Ivory\HttpAdapter\Zend2HttpAdapter;
use Zend\Http\Client;

$zend2HttpAdapter = new Zend2HttpAdapter();
// or
$zend2HttpAdapter = new Zend2HttpAdapter(new Client());
```

Additionally, all adapters accept an additional parameter which represents the message factory. If you want to learn
more, you can read this [doc](/doc/configuration.md#message-factory)
