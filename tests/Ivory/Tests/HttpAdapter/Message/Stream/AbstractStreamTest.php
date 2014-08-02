<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Message\Stream;

/**
 * Abstract stream test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractStreamTest extends \PHPUnit_Framework_TestCase
{
    /** @const string The mode seek disabled. */
    const MODE_SEEK_DISABLED = 'seek';

    /** @const string The mode read disabled. */
    const MODE_READ_DISABLED = 'read';

    /** @const string The mode write disabled. */
    const MODE_WRITE_DISABLED = 'write';

    /** @var \Psr\Http\Message\StreamInterface */
    protected $stream;

    /** @var string */
    protected $string;

    /** @var integer */
    protected $size;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->string = 'abcdefghijklmnopqrstuvwxyz';
        $this->size = strlen($this->string);
        $this->stream = $this->createStream();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->size);
        unset($this->string);
        unset($this->stream);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $this->stream);
    }

    public function testDefaultState()
    {
        $this->assertFalse($this->stream->eof());
        $this->assertSame(0, $this->stream->tell());
        $this->assertSame($this->size, $this->stream->getSize());
        $this->assertTrue($this->stream->isReadable());
        $this->assertTrue($this->stream->isSeekable());
        $this->assertTrue($this->stream->isWritable());
    }

    public function testClose()
    {
        $this->assertTrue($this->stream->close());
        $this->assertFalse($this->stream->eof());
        $this->assertFalse($this->stream->tell());
        $this->assertFalse($this->stream->getContents());
        $this->assertFalse($this->stream->getContents(1));
        $this->assertFalse($this->stream->getSize());
        $this->assertFalse($this->stream->isReadable());
        $this->assertFalse($this->stream->read(1));
        $this->assertFalse($this->stream->isSeekable());
        $this->assertFalse($this->stream->seek(1, SEEK_SET));
        $this->assertFalse($this->stream->seek(1, SEEK_CUR));
        $this->assertFalse($this->stream->seek(1, SEEK_END));
        $this->assertFalse($this->stream->isWritable());
        $this->assertFalse($this->stream->write('foo'));
    }

    public function testMultipleClose()
    {
        $this->assertTrue($this->stream->close());
        $this->assertFalse($this->stream->close());
    }

    public function testDetach()
    {
        $this->assertTrue($this->stream->detach());
        $this->assertFalse($this->stream->eof());
        $this->assertFalse($this->stream->tell());
        $this->assertFalse($this->stream->getContents());
        $this->assertFalse($this->stream->getContents(1));
        $this->assertFalse($this->stream->getSize());
        $this->assertFalse($this->stream->isReadable());
        $this->assertFalse($this->stream->read(1));
        $this->assertFalse($this->stream->isSeekable());
        $this->assertFalse($this->stream->seek(1, SEEK_SET));
        $this->assertFalse($this->stream->seek(1, SEEK_CUR));
        $this->assertFalse($this->stream->seek(1, SEEK_END));
        $this->assertFalse($this->stream->isWritable());
        $this->assertFalse($this->stream->write('foo'));
    }

    public function testMultipleDetach()
    {
        $this->assertTrue($this->stream->detach());
        $this->assertFalse($this->stream->detach());
    }

    public function testEof()
    {
        $this->stream->getContents();

        $this->assertTrue($this->stream->eof());
    }

    public function testTellWithoutSeek()
    {
        $this->assertSame(0, $this->stream->tell());
    }

    public function testTellWithSeek()
    {
        $this->assertTrue($this->stream->seek($offset = 10));
        $this->assertSame($offset, $this->stream->tell());
    }

    public function testGetSize()
    {
        $this->assertSame($this->size, $this->stream->getSize());
    }

    public function testMultipleGetSize()
    {
        $this->assertSame($this->size, $this->stream->getSize());
        $this->assertSame($this->size, $this->stream->getSize());
    }

    public function testSeekWithSeekSet()
    {
        $this->assertTrue($this->stream->seek($offset = 10, SEEK_SET));

        $this->assertSame($offset, $this->stream->tell());
    }

    public function testSeekWithSeekSetAndOffsetTooBig()
    {
        $this->assertTrue($this->stream->seek($offset = 100, SEEK_SET));

        $this->assertSame($offset, $this->stream->tell());
        $this->assertSame($this->size, $this->stream->getSize());
    }

    public function testSeekWithSeekSetAndOffsetTooSmall()
    {
        $this->assertTrue($this->stream->seek($offset = 10, SEEK_SET));
        $this->assertFalse($this->stream->seek(-100, SEEK_SET));

        $this->assertSame($offset, $this->stream->tell());
    }

    public function testSeekWithSeekCur()
    {
        $this->assertTrue($this->stream->seek($offsetSet = 10, SEEK_SET));
        $this->assertTrue($this->stream->seek($offsetCur = 10, SEEK_CUR));

        $this->assertSame($offsetSet + $offsetCur, $this->stream->tell());
    }

    public function testSeekWithSeekCurAndOffsetTooBig()
    {
        $this->assertTrue($this->stream->seek($offsetSet = 10, SEEK_SET));
        $this->assertTrue($this->stream->seek($offsetCur = 100, SEEK_CUR));

        $this->assertSame($offsetSet + $offsetCur, $this->stream->tell());
        $this->assertSame($this->size, $this->stream->getSize());
    }

    public function testSeekWithSeekCurAndOffsetTooSmall()
    {
        $this->assertTrue($this->stream->seek($offset = 10, SEEK_SET));
        $this->assertFalse($this->stream->seek(-100, SEEK_CUR));

        $this->assertSame($offset, $this->stream->tell());
    }

    public function testSeekWithSeekEnd()
    {
        $this->assertTrue($this->stream->seek($offset = -10, SEEK_END));

        $this->assertSame($this->size + $offset, $this->stream->tell());
    }

    public function testSeekWithSeekEndAndOffsetTooBig()
    {
        $this->assertTrue($this->stream->seek($offset = 100, SEEK_END));

        $this->assertSame($this->size + $offset, $this->stream->tell());
        $this->assertSame($this->size, $this->stream->getSize());
    }

    public function testSeekWithSeekEndAndOffsetTooSmall()
    {
        $this->assertTrue($this->stream->seek($offset = 10, SEEK_SET));
        $this->assertFalse($this->stream->seek(-100, SEEK_END));

        $this->assertSame($offset, $this->stream->tell());
    }

    public function testModeSeekDisabled()
    {
        $this->stream = $this->createStream(self::MODE_SEEK_DISABLED);

        $this->assertFalse($this->stream->isSeekable());
        $this->assertFalse($this->stream->seek(10));
    }

    public function testReadWithoutSeek()
    {
        $length = 10;

        $this->assertSame(substr($this->string, 0, $length), $this->stream->read($length));
        $this->assertSame($length, $this->stream->tell());
    }

    public function testReadWithoutSeekAndLengthTooBig()
    {
        $length = 100;

        $this->assertSame(substr($this->string, 0, $length), $this->stream->read($length));
        $this->assertSame($this->size, $this->stream->tell());
        $this->assertSame($this->size, $this->stream->getSize());
    }

    public function testReadWithSeek()
    {
        $this->stream->seek($offset = 10);
        $length = 10;

        $this->assertSame(substr($this->string, $offset, $length), $this->stream->read($length));
        $this->assertSame($offset + $length, $this->stream->tell());
    }

    public function testModeReadDisabled()
    {
        $this->stream = $this->createStream(self::MODE_READ_DISABLED);

        $this->assertFalse($this->stream->isReadable());
        $this->assertFalse($this->stream->read(10));
        $this->assertFalse($this->stream->getContents());
        $this->assertSame('', (string) $this->stream);
    }

    public function testWriteWithoutSeek()
    {
        $string = 'foo';
        $stringSize = strlen($string);

        $this->assertSame($stringSize, $this->stream->write($string));

        $this->assertSame(substr_replace($this->stream, $string, 0, $stringSize), (string) $this->stream);
        $this->assertSame($stringSize, $this->stream->tell());
    }

    public function testWriteWithoutSeekAndStringSizeGreaterThanSize()
    {
        $string = str_repeat('foo', 10);
        $stringSize = strlen($string);

        $this->assertSame($stringSize, $this->stream->write($string));

        $this->assertSame(substr_replace($this->stream, $string, 0, $stringSize), (string) $this->stream);
        $this->assertSame($stringSize, $this->stream->tell());
        $this->assertSame($stringSize, $this->stream->getSize());
    }

    public function testWriteWithSeek()
    {
        $string = 'foo';
        $stringSize = strlen($string);

        $this->stream->seek($offset = 10);

        $this->assertSame($stringSize, $this->stream->write($string));

        $this->assertSame(substr_replace($this->stream, $string, $offset, $stringSize), (string) $this->stream);
        $this->assertSame($offset + $stringSize, $this->stream->tell());
    }

    public function testWriteWithSeekTooBig()
    {
        $string = 'foo';
        $stringSize = strlen($string);

        $this->stream->seek($offset = 100);

        $this->string .= str_repeat("\0", $offset - $this->size);

        $this->assertSame($stringSize, $this->stream->write($string));

        $this->assertSame(substr_replace($this->string, $string, $offset, $stringSize), (string) $this->stream);
        $this->assertSame($offset + $stringSize, $this->stream->tell());
        $this->assertSame($offset + $stringSize, $this->stream->getSize());
    }

    public function testModeWriteDisabled()
    {
        $this->stream = $this->createStream(self::MODE_WRITE_DISABLED);

        $this->assertFalse($this->stream->isWritable());
        $this->assertFalse($this->stream->write('foo'));
    }

    public function testGetContentsWithoutMaxLength()
    {
        $this->assertSame($this->string, $this->stream->getContents());
        $this->assertSame($this->size, $this->stream->tell());
    }

    public function testGetContentsWithMaxLength()
    {
        $maxLength = 10;

        $this->assertSame(substr($this->string, 0, $maxLength), $this->stream->getContents($maxLength));
        $this->assertSame($maxLength, $this->stream->tell());
    }

    public function testGetContentsWithMaxLengthBiggerThanSize()
    {
        $maxLength = 100;

        $this->assertSame(substr($this->string, 0, $maxLength), $this->stream->getContents($maxLength));
        $this->assertSame($this->size, $this->stream->tell());
    }

    public function testGetContentsWithoutMaxLengthAndWithSeek()
    {
        $this->stream->seek($offset = 10);

        $this->assertSame(substr($this->string, $offset), $this->stream->getContents());
        $this->assertSame($this->size, $this->stream->tell());
    }

    public function testGetContentsWithMaxLengthAndSeek()
    {
        $this->stream->seek($offset = 10);
        $maxLength = 10;

        $this->assertSame(substr($this->string, $offset, $maxLength), $this->stream->getContents($maxLength));
        $this->assertSame($offset + $maxLength, $this->stream->tell());
    }

    public function testToStringWithoutSeek()
    {
        $this->assertSame($this->string, (string) $this->stream);
        $this->assertSame(0, $this->stream->tell());
    }

    public function testToStringWithSeek()
    {
        $this->stream->seek($offset = 10);

        $this->assertSame($this->string, (string) $this->stream);
        $this->assertSame($offset, $this->stream->tell());
    }

    /**
     * Creates the stream.
     *
     * @param string|null $mode The stream mode.
     *
     * @return \Psr\Http\Message\StreamInterface The stream.
     */
    abstract protected function createStream($mode = null);
}
