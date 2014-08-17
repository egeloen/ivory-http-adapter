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

## Raw Datas

The raw datas represent the payload sent to the server without any formatting. That means if you want to send for
example json datas or xml datas, you should rely on it but you will need to explicitely set the content-type of the
request.

``` php
$hasRawDatas = $internalRequest->hasRawDatas();
$rawDatas = $internalRequest->getRawDatas();
$internalRequest->setRawDatas($rawDatas);
$internalRequest->clearRawDatas();
```

Be aware that if you use raw datas, you can't use datas/files and vice versa.

## Datas

The datas represent the form datas which will be sent to the server. It is an associative array describing name/value
pairs.

``` php
$hasDatas = $internalRequest->hasDatas();
$datas = $internalRequest->getDatas();
$internalRequest->setDatas(array('foo' => 'bar');
$internalRequest->addDatas(array('foo' => 'bar');
$internalRequest->removeDatas(array('foo');
$internalRequest->clearDatas();

$hasData = $internalRequest->hasData('foo');
$data = $internalRequest->getData('foo');
$internalRequest->setData('foo', 'bar');
$internalRequest->addData('foo', 'bar');
$internalRequest->removeData('foo');
```

## Files

The files represents the form files which will be sent to the server. It is an associative array describing a name/file
path pair.

``` php
$hasFiles = $internalRequest->hasFiles();
$files = $internalRequest->getFiles();
$internalRequest->setFiles(array('file' => '/path/of/the/file'));
$internalRequest->addFiles(array('file' => '/path/of/the/file'));
$internalRequest->removeFiles(array('file'));
$internalRequest->clearFiles();

$hasFile = $internalRequest->hasFile('file');
$file = $internalRequest->getFile('file');
$internalRequest->setFile('file', '/path/of/the/file');
$internalRequest->addFile('file', '/path/of/the/file');
$internalRequest->removeFile('file');
```

## Parameters

The parameters work the same way as the request one. If you want to learn more about it, you can read this
[doc](/doc/request.md#parameters).
