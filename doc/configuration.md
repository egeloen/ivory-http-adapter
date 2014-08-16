# Configuration

The available configuration is defined through the `Ivory\HttpAdapter\HttpAdapterConfigInterface`. All adapters are
able to be configured as explain above.

## Message factory

The message factory allows to create a PSR-7 request, an internal request and an empty response. So, if you want to
use your own classes in order to add extra behaviors, you can define your own and instantiate it in your custom
factory which implements the `Ivory\HttpAdapter\Message\MessageFactoryInterface` or extends the
`Ivory\HttpAdapter\Message\MessageFactory`. Then, to get/set it, you can use:

``` php
use My\MessageFactory;

$messageFactory = $httpAdapter->getMessageFactory();
$httpAdapter->setMessageFactory(new MessageFactory());
```

## Event dispatcher

The event dispatcher allows you to attach listeners/subscribers in order to hook into the available events. To get/set
it, you can use:

``` php
$eventDispatcher = $httpAdapter->getEventDispatcher();
$httpAdapter->setEventDispatcher($eventDispatcher);
```

If you want to learn more about the events, you can read this [doc](/doc/events.md).

## Protocol version

The protocol version defines the version for the http request sent (1.0 or 1.1, default: 1.1). If you want to get/set
it, you can use:

``` php
use Ivory\HttpAdapter\Message\RequestInterface;

$protocolVersion = $httpAdapter->getProtocolVersion();

$httpAdapter->setProtocolVersion(RequestInterface::PROTOCOL_VERSION_10);
// or
$httpAdapter->setProtocolVersion(RequestInterface::PROTOCOL_VERSION_11);
```

## Keep alive

The keep alive flag allows to define if the connection should be kept alive or not (default: false). Basically, if you
don't provide the `connection` header, it will be automatically populated by the library according to the keep alive
flag. So, if you provide the `connection` headers, the keep alive flag is ignored. If you want to get/set it, you can
use:

``` php
$keepAlive = $httpAdapter->getKeepAlive();
$httpAdapter->setKeepAlive(true);
```

## Encoding type

The encoding type defines the encoding of the request (url encoded, form data or none). The content type is
automatically populated according to the datas/files you provide but if you encode yourself the datas as string, you
need to set it explicitely or pass the `content-type` header yourself. Then, if you want to get/set it, you can use:

``` php
$hasEncodingType = $httpAdapter->hasEncodingType();
$encodingType = $httpAdapter->getEncodingType();

$httpAdapter->setEncodingType(HttpAdapterConfigInterface::ENCODING_TYPE_URLENCODED);
// or
$httpAdapter->setEncodingType(HttpAdapterConfigInterface::ENCODING_TYPE_FORMDATA);
// or
$httpAdapter->setEncodingType(null);
```

## Boundary

The boundary is a complement to the encoding type. If you configure it with form data, the multipart payload is
separated by a boundary which needs to be append to the `content-type` header. If you provide datas/files, it will be
automatically populated but, if you encode yourself the datas as string, you need to set it explicitely or pass the
`content-type` header yourself. Then, if you want to get/set it, you can use:

``` php
$boundary = $httpAdapter->getBoundary();
$httpAdapter->setBoundary('abcdefg');
```

## Timeout

The timeout defines the maximum number of seconds the connection should be active since we consider it invalid
(default: 10). If you want to get/set it, you can use:

``` php
$timeout = $httpAdapter->getTimeout();
$httpAdapter->setTimeout(30);
```

## Maximum redirection

The maximum redirects allows to configure the number of redirects the http adapter is allowed to follow (default: 5).
If you want to get/set it, you can use:

``` php
$hasMaxRedirects = $httpAdapter->hasMaxRedirects();
$maxRedirects = $httpAdapter->getMaxRedirects();
$httpAdapter->setMaxRedirects(10);
```

If you want to disable it, just set it to zero.
