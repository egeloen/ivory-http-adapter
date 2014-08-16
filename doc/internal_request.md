# Internal Request

The internal request is a specialized request allowing to directly manage datas and files instead of relying on the
stream body. Basically, every request you do on the http adapter will be converted internally to this kind of request
and will be passed to each specialized http adapter in order to be processed. An internal request is represented by the
`Ivory\HttpAdapter\Message\InternalRequest`, it implements the `Ivory\HttpAdapter\Message\InternalRequestInterface`
and extends the `Ivory\HttpAdapter\Message\Request` but can be customized as explained
[here](/doc/configuration.md#message-factory). So everything explains in the [request doc](/doc/request.md) is still
relevant except than you can't play with body anymore (the related methods are disabled and throw an exception).

## Create your internal request

``` php
use Ivory\HttpAdapter\Message\InternalRequest;

$internalRequest = new InternalRequest($url, $method);
```

An internal request needs at least an url and a method.

## Protocol version

The protocol version works the same way as the request one. If you want to learn more about it, you can read this
[doc](/doc/request.md#protocol-version).

## Url

The url works the same way as the request one. If you want to learn more about it, you can read this
[doc](/doc/request.md#url).

## Method

The method works the same way as the request one. If you want to learn more about it, you can read this
[doc](/doc/request.md#method).

## Headers

The headers work the same way as the request one. If you want to learn more about it, you can read this
[doc](/doc/request.md#headers).

## Datas

The datas represent the body datas which will be sent. It can be either an associative array or a string. If you rely
on string, your can't provide files as the library can't guess what type of encoding your using. Then, if you rely on
string, you will need to set the content-type in the headers or on the http adapter by yourself. If you want to
get/set it, you can use:

``` php
$hasDatas = $internalRequest->hasDatas();
$hasStringDatas = $internalRequest->hasStringDatas();
$hasArrayDatas = $internalRequest->hasArrayDatas();

$datas = $internalRequest->getDatas();
$internalRequest->setDatas(array('foo' => 'bar');
// or
$internalRequest->setDatas('string');
```

## Files

The files represents the body files which will be sent. It is an associative array describing a field/absolute file
path pair. Be aware, it is not possible to use files and datas as string. If you want to use it, you can use:

``` php
$hasFiles = $internalRequest->hasFiles();
$files = $internalRequest->getFiles();
$internalRequest->setFiles(array('file' => '/path/of/the/file');
```
