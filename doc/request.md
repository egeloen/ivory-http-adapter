# Request

The request class follows the [PSR-7 Standard](https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md).
That means it implements the `Psr\Http\Message\OutgoingRequestInterface`. Additionally, the library adds some features
on top of it through the `Ivory\HttpAdapter\Message\MessageInterface` and `Ivory\HttpAdapter\Message\RequestInterface`
but can be customized as explained [here](/doc/configuration.md#message-factory).

## Create your request

``` php
use Ivory\HttpAdapter\Message\Request;

$request = new Request($url, $method);
```

A request needs at least an url and a method.

## Protocol version

The protocol version defines the version for the http request sent (1.0 or 1.1, default: 1.1). If you want to get/set
it, you can use:

``` php
use Ivory\HttpAdapter\Message\RequestInterface;

$protocolVersion = $request->getProtocolVersion();

$request->setProtocolVersion(RequestInterface::PROTOCOL_VERSION_1_0);
// or
$request->setProtocolVersion(RequestInterface::PROTOCOL_VERSION_1_1);
```

Note that the request protocol version will be used instead of the one configured on the http adapter.

## Url

The url defines the remote server where the request will be sent. It can be either a string or an object implementing
the `__toString` method. If you want to get/set it, you can use:

``` php
$url = $request->getUrl();
$request->setUrl('http://egeloen.fr/');
// or
$request->setUrl($object);
```

## Method

The method defines the http verb used for the request. If you want to get/set it, you can use:

``` php
use Ivory\HttpAdapter\Message\RequestInterface;

$method = $request->getMethod();
$request->setMethod(RequestInterface::METHOD_GET);
```

All methods are described by the `Ivory\HttpAdapter\Message\RequestInterface::METHOD_*` constants.

## Headers

The headers defines the metadatas which will be sent to the remote address. If you want to get/set them, you can use:

``` php
$hasHeaders = $request->hasHeaders();
$headers = $request->getHeaders();
$request->setHeaders(array(
    'connection'      => 'close',
    'accept-language' => array('en', 'fr'),
));
$request->addHeaders(array('accept-language' => 'it'));
$request->removeHeaders(array('connection', 'accept-language'));

$hasHeader = $request->hasHeader('connection');
$header = $request->getHeader('connection');
$headerAsArray = $request->getHeaderAsArray('connection');
$request->setHeader('connection', 'close');
$request->addHeader('accept-language', 'pt');
$request->removeHeader('connection');
```

## Body

The body represents the content of the request and is defined by the `Psr\Http\Message\StreamableInterface`. If you
want to get/set it, you can use:

``` php
$hasBody = $request->hasBody();
$body = $request->getBody();

$request->setBody($body);
// or
$request->setBody(null);
```

If you want to learn more about the stream body, you can read this [doc](/doc/stream.md).

## Parameters

The parameters represents extra datas which can be passed to the http adapter (it is only used internally).
To check/get/set them, you can use:

``` php
$hasParameters = $request->hasParameters();
$parameters = $request->getParameters();
$request->setParameters($parameters);
$request->addParameters($parameters);
$request->removeParameters($names);
$request->clearParameters();

$hasParameter = $request->hasParameter($name);
$parameter = $request->getParameter($name);
$request->setParameter($name, $value);
$request->addParameter($name, $value);
$request->removeParameter($name);
```
