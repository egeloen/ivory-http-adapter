# Stream

A stream represents the body of a request or a response. All stream follows the
[PSR-7 Standard](https://github.com/php-fig/fig-standards/blob/master/proposed/http-message.md). That means they
implement the `Psr\Http\Message\StreamableInterface`.

## API

You can use a stream with the following API:

``` php
$result = $stream->close();
$resource = $stream->detach();
$stream->attach($resource);

$metadatas = $stream->getMetadata();
$metadata = $stream->getMetadata('seekable');

$eof = $stream->eof();
$position = $stream->tell();

$size = $stream->getSize();

$isSeekable = $stream->isSeekable();

$result = seek($offset);
// or
$result = seek($offset, SEEK_CUR);

$isReadable = $stream->isReadable();
$string = $stream->read(10);

$isWritable = $stream->isWritable();
$result = $stream->write('string');

$contents = $stream->getContents();
// or
$contents = $stream->getContents(10);

$fullContents = (string) $stream;
```

## Resource stream

A resource stream allows to manipulate a resource. To create one:

``` php
use Ivory\HttpAdapter\Message\Stream\ResourceStream;

$stream = new ResourceStream($resource);
```

## String stream

A string stream allows to manipulate a string. To create one:

``` php
use Ivory\HttpAdapter\Message\Stream\StringStream;

$stream = new StringStream($string);
// or
$stream = new StringStream($string, StringStream::MODE_SEEK & StringStream:MODE_READ & StringStream::MODE_WRITE);
```

The second paramter represents the capability of the stream (seekable, readable and/or writable). By default, it has
a full capability but a string stream wraps in a response can only be seek/read.

## Guzzle stream

A guzzle stream allows to manipulate a real guzzle stream. To create one:

``` php
use Guzzle\Stream\Stream;
use Ivory\HttpAdapter\Message\Stream\GuzzleStream;

$stream = new GuzzleStream(new Stream($resource));
```

## Guzzle http stream

A guzzle http stream allows to manipulate a real guzzle http stream. To create one:

``` php
use GuzzleHttp\Stream\Stream;
use Ivory\HttpAdapter\Message\Stream\GuzzleHttpStream;

$stream = new GuzzleHttpStream(new Stream($resource));
```
