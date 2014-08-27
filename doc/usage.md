# Usage

To send a request, you can use the API defined by the `Ivory\HttpAdapter\HttpAdapterInterface`. All these methods
throw an `Ivory\HttpAdapter\HttpAdapterException` if an error occurred (I would recommend you to always use a try/catch
block everywhere) and return an `Ivory\HttpAdapter\Message\ResponseInterface`. If you want to learn more about the
response, you can read this [doc](/doc/response.md).

Additionally, the url can be a string or an object implementing the `__toString` method. The headers parameter can be
an associative array describing an header key/value pair or an indexed array already formatted. The datas can be an
associative array or a string already formatted according to the content-type you want to use. Finally, the files are
an associative array describing key/path pair.

## Send a GET request

``` php

$response = $httpAdapter->get($url, $headers);
```

## Send an HEAD request

``` php
$response = $httpAdapter->head($url, $headers);
```

## Send a TRACE request

``` php
$response = $httpAdapter->trace($url, $headers);
```

## Send a POST request

``` php
$response = $httpAdapter->post($url, $headers, $datas, $files);
```

## Send a PUT request

``` php
$response = $httpAdapter->put($url, $headers, $datas, $files);
```

## Send a PATCH request

``` php
$response = $httpAdapter->patch($url, $headers, $datas, $files);
```

## Send a DELETE request

``` php
$response = $httpAdapter->delete($url, $headers, $datas, $files);
```

## Send an OPTIONS request

``` php
$response = $httpAdapter->options($url, $headers, $datas, $files);
```

## Send a request

``` php
$response = $httpAdapter->send($url, $method, $headers, $datas, $files);
```

All methods are described by the `Ivory\HttpAdapter\Message\RequestInterface::METHOD_*` constants.

## Send a PSR-7 request

``` php
use Ivory\HttpAdapter\Message\Request;

$response = $httpAdapter->sendRequest(new Request($url, $method));
```

If you want to learn more about the `Ivory\HttpAdapter\Message\Request`, your can read this [doc](/doc/request.md).

## Send an internal request

``` php
use Ivory\HttpAdapter\Message\InternalRequest;

$response = $httpAdapter->sendInternalRequest(new InternalRequest($url, $method));
// or
$response = $httpAdapter->sendRequest(new InternalRequest($url, $method));
```

If you want to learn more about the `Ivory\HttpAdapter\Message\InternalRequest`, your can read this
[doc](/doc/internal_request.md).
