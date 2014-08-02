# Response

The response represents the result of all http adapter sends. It is represented by the
`Ivory\HttpAdapter\Message\Response` but can be customized as explained [here](/doc/configuration.md#message-factory).
Like the request, the response follows the [PSR-7 Standard](https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md).
That means it implements the `Psr\Http\Message\RespondeInterface`. Additionally, the library adds some features on top of
it through the `Ivory\HttpAdapter\Message\MessageInterface` and `Ivory\HttpAdapter\Message\ResponseInterface`.

## Protocol version

The protocol version represents the version for the http response received (1.0 or 1.1). If you want to get/set it,
you can use:

``` php
use Ivory\HttpAdapter\Message\ResponseInterface;

$protocolVersion = $response->getProtocolVersion();

$response->setProtocolVersion(ResponseInterface::PROTOCOL_VERSION_10);
// or
$response->setProtocolVersion(ResponseInterface::PROTOCOL_VERSION_11);
```

## Status code

The status code represents the code for the http response received. If you want to get/set it, you can use:

``` php
$statusCode = $response->getStatusCode();
$response->setStatusCode(200);
```

## Reason phrase

The reason phrase represents the sentence associated to the status code received. It you want to get/set it, you can
use:

``` php
$reasonPhrase = $response->getReasonPhrase();
$response->setReasonPhrase('OK');
```

## Headers

The headers represents the metadatas which have be received. If you want to get/set them, you can use:

``` php
$hasHeaders = $response->hasHeaders();
$headers = $response->getHeaders();
$response->setHeaders(array(
    'connection'      => 'close',
    'accept-language' => array('en', 'fr'),
));
$response->addHeaders(array('accept-language' => 'it'));
$response->removeHeaders(array('connection', 'accept-language'));

$hasHeader = $response->hasHeader('connection');
$header = $response->getHeader('connection');
$headerAsArray = $response->getHeaderAsArray('connection');
$response->setHeader('connection', 'close');
$response->addHeader('accept-language', 'pt');
$response->removeHeader('connection');
```

## Body

The body represents the content of the response and is defined by the `Psr\Http\Message\StreamInterface`. If you want to
get/set it, you can use:

``` php
$hasBody = $request->hasBody();
$body = $request->getBody();

$request->setBody($body);
// or
$request->setBody(null);
```

If you want to learn more about the stream body, you can read this [doc](/doc/stream.md).

## Effective url

The effective url represents the final url reached according to the max redirects configuration. Then, it is only
usefull when there is a redirect response and the max redirects features is enabled. If you want to get/set it, you can
use:

``` php
$effectiveUrl = $response->getEffectiveUrl();
$response->setEffectiveUrl('http://egeloen.fr');
```

Additionally, the effective url is not supported by all adapters. Buzz, Httpful and Zend2 does not support it, they
will always return the original url as effective url.
