# Response

The response represents the result of all http adapter sends. It is represented by the
`Ivory\HttpAdapter\Message\Response` but can be customized as explained [here](/doc/configuration.md#message-factory).
Like the request, the response follows the [PSR-7 Standard](https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md).
That means it implements the `Psr\Http\Message\IncomingResponseInterface`. Additionally, the library adds some features
on top of it through the `Ivory\HttpAdapter\Message\MessageInterface` and `Ivory\HttpAdapter\Message\ResponseInterface`.

## Protocol version

The protocol version represents the version for the http response received (1.0 or 1.1). If you want to get it, you can
use:

``` php
use Ivory\HttpAdapter\Message\ResponseInterface;

$protocolVersion = $response->getProtocolVersion();
```

## Status code

The status code represents the code for the http response received. If you want to get it, you can use:

``` php
$statusCode = $response->getStatusCode();
```

## Reason phrase

The reason phrase represents the sentence associated to the status code received. It you want to get it, you can use:

``` php
$reasonPhrase = $response->getReasonPhrase();
```

## Headers

The headers represents the metadatas which have be received. If you want to get them, you can use:

``` php
$hasHeaders = $response->hasHeaders();
$headers = $response->getHeaders();
$response->setHeaders(array(
    'connection'      => 'close',
    'accept-language' => array('en', 'fr'),
));

$hasHeader = $response->hasHeader('connection');
$header = $response->getHeader('connection');
$headerAsArray = $response->getHeaderAsArray('connection');
```

## Body

The body represents the content of the response and is defined by the `Psr\Http\Message\StreamableInterface`. If you
want to get it, you can use:

``` php
$hasBody = $request->hasBody();
$body = $request->getBody();
```

If you want to learn more about the stream body, you can read this [doc](/doc/stream.md).

## Parameters

The parameters represents extra datas which are returned by the http adapter. To check/get/set them, you can use:

``` php
$hasParameters = $response->hasParameters();
$parameters = $response->getParameters();
$response->setParameters($parameters);
$response->addParameters($parameters);
$response->removeParameters($names);
$response->clearParameters();

$hasParameter = $response->hasParameter($name);
$parameter = $response->getParameter($name);
$response->setParameter($name, $value);
$response->addParameter($name, $value);
$response->removeParameter($name);
```
