# Configuration

The available configuration is defined through the `Ivory\HttpAdapter\ConfigurationInterface` and its default
implementation is the `Ivory\HttpAdapter\Configuration`. The configuration can be passed to all adapters as last
constructor parameters or via getter/setter and allow you to configure the them as explain above:

``` php
$curlHttpAdapter = new CurlHttpAdapter(new Configuration());
// or
$zend1HttpAdapter = new Zend1HttpAdapter(null, new Configuration());
// or
$configuration = $httpAdapter->getConfiguration();
$httpAdapter->setConfiguration($configuration);
```

## Message factory

The message factory allows to create a PSR-7 request, an internal request and a response. So, if you want to
use your own classes in order to add extra behaviors, you can define your own and instantiate it in your custom
factory which implements the `Ivory\HttpAdapter\Message\MessageFactoryInterface` or extends the
`Ivory\HttpAdapter\Message\MessageFactory`. Then, to get/set it, you can use:

``` php
use My\MessageFactory;

$messageFactory = $configuration->getMessageFactory();
$configuration->setMessageFactory(new MessageFactory());
// or
$configuration = new Configuration($messageFactory);
```

## Protocol version

The protocol version defines the version for the http request sent (1.0 or 1.1, default: 1.1). If you want to get/set
it, you can use:

``` php
use Ivory\HttpAdapter\Message\RequestInterface;

$protocolVersion = $configuration->getProtocolVersion();

$configuration->setProtocolVersion(RequestInterface::PROTOCOL_VERSION_1_0);
// or
$configuration->setProtocolVersion(RequestInterface::PROTOCOL_VERSION_1_1);
```

## Keep alive

The keep alive flag allows to define if the connection should be kept alive or not (default: false). Basically, if you
don't provide the `connection` header, it will be automatically populated by the library according to the keep alive
flag. So, if you provide the `connection` headers, the keep alive flag is ignored. If you want to get/set it, you can
use:

``` php
$keepAlive = $configuration->getKeepAlive();
$configuration->setKeepAlive(true);
```

## Encoding type

The encoding type defines the encoding of the request (url encoded, form data or none). The content type is
automatically populated according to the datas/files you provide but if you encode yourself the datas as string, you
need to set it explicitely or pass the `content-type` header yourself. Then, if you want to get/set it, you can use:

``` php
$hasEncodingType = $configuration->hasEncodingType();
$encodingType = $configuration->getEncodingType();

$configuration->setEncodingType(HttpAdapterConfigInterface::ENCODING_TYPE_URLENCODED);
// or
$configuration->setEncodingType(HttpAdapterConfigInterface::ENCODING_TYPE_FORMDATA);
// or
$configuration->setEncodingType(null);
```

## Boundary

The boundary is a complement to the encoding type. If you configure it with form data, the multipart payload is
separated by a boundary which needs to be append to the `content-type` header. If you provide datas/files, it will be
automatically populated but, if you encode yourself the datas as string, you need to set it explicitely or pass the
`content-type` header yourself. Then, if you want to get/set it, you can use:

``` php
$boundary = $configuration->getBoundary();
$configuration->setBoundary('abcdefg');
```

## Timeout

The timeout defines the maximum number of seconds the connection should be active since we consider it invalid
(default: 10). If you want to get/set it, you can use:

``` php
$timeout = $configuration->getTimeout();
$configuration->setTimeout(30);
```

## User Agent

The user agent defines which client have sent the request. For example, each browsers send a specific user agent in
order to identify it. By default, all http adapters send the `Ivory Http Adapter` user agent but if you want to
change it, you can use:

``` php
$userAgent = $configuration->getUserAgent();
$configuration->setUserAgent('My user agent');
```

## Base url

If set, requests created using a relative url are combined with the configured base url. Requests created using an
absolute url are not affected by this setting.

``` php
$hasBaseUrl = $configuration->hasBaseUrl();
$baseUrl = $configuration->getBaseUrl();

$configuration->setBaseUrl('http://api.example.com');

// Example
$response = $http->get('/path/to/resource');
```
