# PSR-7 (Http)

For http messaging, the library is based on the [PSR-7 Standard](https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md) 
by using the [zendframework/zend-diactoros](https://github.com/zendframework/zend-diactoros) package.

## Factory

The PSR-7 only defines interfaces and zendframework/zend-diactoros only defines implementation. The library is shipped
with a factory which ease the creation of requests/responses. As explained in the configuration
[doc](/doc/configuration.md#messafe-factory), the requests/responses are created through a factory, so, if you want
to create any PSR-7 objects, it is recommended to use the following API:

``` php
$request = $messageFactory->createRequest(
    $uri,
    $method,
    $protocolVersion,
    $headers,
    $body,
    $parameters
);

$internalRequest = $messageFactory->createInternalRequest(
    $uri,
    $method,
    $protocolVersion,
    $headers,
    $datas,
    $files,
    $parameters
);

$response = $messageFactory->createResponse(
    $statusCode,
    $protocolVersion,
    $headers,
    $body,
    $parameters
);
```

## Message

The message implementation is based on `Zend\Diactoros\MessageTrait` with some features on top of it through the
`Ivory\HttpAdapter\Message\MessageTrait` such as parameters. Basically, parameters are arbitrary values that you can 
store in your message. They are mostly used by the event system in order to store additional informations.

The available API is:

``` php
$parameters = $message->getParameters();
$hasParameter = $message->hasParameter($name);
$parameter = $message->getParameter($name);
$newMessage = $message->withParameter($name, $value);
$newMessage = $message->withoutParameter($name);
```

## Request

The request is based on `Zend\Diactoros\Request` with additionally the Ivory message features.

## Internal Request

The internal request is an extension of the request. It can be used as a request or as form. Basically, if you only use 
the API provided by the PSR-7 standard, it is your responsibility to encode the request body. Given, you want to send a 
simple form, you will need to encode the body as `application/x-www-form-urlencoded` or even worse, if you want to 
upload files, you will need to encode the body as `multipart/form-data`. Hopefully, the internal request is here and 
allow you to easily deal with such problematic.

Be aware that if you use body, the datas/files will be ignored even if you provide them (the PSR request always win).

### Datas

The datas represent the form datas which will be sent to the server. It is an associative array describing name/value
pairs:

``` php
$datas = $internalRequest->getDatas();
$hasData = $internalRequest->hasData('foo');
$data = $internalRequest->getData('foo');
$newInternalRequest = $internalRequest->withData('foo', 'bar')
$newInternalRequest = $internalRequest->withoutData('foo');
```

### Files

The files represents the form files which will be sent to the server. It is an associative array describing a name/file
path pair:

``` php
$files = $internalRequest->getFiles();
$hasFile = $internalRequest->hasFile('file');
$file = $internalRequest->getFile('file');
$newInternalRequest = $internalRequest->withFile('file', '/path/of/the/file');
$newInternalRequest = $internalRequest->withoutFile('file');
```

## Response

The response is based on `Zend\Diactoros\Response` with additionally the Ivory message features.

## Stream

The response is based on `Zend\Diactoros\Stream`.
