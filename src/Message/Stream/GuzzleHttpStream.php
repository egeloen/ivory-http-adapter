<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Message\Stream;

use GuzzleHttp\Stream\StreamInterface;
use Ivory\HttpAdapter\HttpAdapterException;

/**
 * Guzzle http stream.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class GuzzleHttpStream extends AbstractStream
{
    /** @var \GuzzleHttp\Stream\StreamInterface */
    private $stream;

    /**
     * Creates a guzzle stream.
     *
     * @param \GuzzleHttp\Stream\StreamInterface $stream
     */
    public function __construct(StreamInterface $stream)
    {
        $this->attach($stream);
    }

    /**
     * {@inheritdoc}
     */
    protected function hasValue()
    {
        return $this->stream !== null
            && ($this->stream->isReadable() !== false
            || $this->stream->isSeekable() !== false
            || $this->stream->isWritable() !== false);
    }

    /**
     * {@inheritdoc}
     */
    protected function doClose()
    {
        $this->stream->close();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doAttach($stream)
    {
        if (!$stream instanceof StreamInterface) {
            throw HttpAdapterException::streamIsNotValid(
                $stream,
                get_class($this),
                'GuzzleHttp\Stream\StreamInterface'
            );
        }

        $this->stream = $stream;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDetach()
    {
        return $this->stream->detach();
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetMetadata($key = null)
    {
        return method_exists($this->stream, 'getMetadata')
            ? $this->stream->getMetadata($key)
            : ($key !== null ? null : array());
    }

    /**
     * {@inheritdoc}
     */
    protected function doEof()
    {
        return $this->stream->eof();
    }

    /**
     * {@inheritdoc}
     */
    protected function doTell()
    {
        return $this->stream->tell();
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetSize()
    {
        return $this->stream->getSize();
    }

    /**
     * {@inheritdoc}
     */
    protected function doIsSeekable()
    {
        return $this->stream->isSeekable();
    }

    /**
     * {@inheritdoc}
     */
    protected function doSeek($offset, $whence)
    {
        return $this->stream->seek($offset, $whence);
    }

    /**
     * {@inheritdoc}
     */
    protected function doIsReadable()
    {
        return $this->stream->isReadable();
    }

    /**
     * {@inheritdoc}
     */
    protected function doRead($length)
    {
        return $this->stream->read($length);
    }

    /**
     * {@inheritdoc}
     */
    protected function doIsWritable()
    {
        return $this->stream->isWritable();
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite($string)
    {
        return $this->stream->write($string);
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetContents()
    {
        return $this->stream->getContents();
    }

    /**
     * {@inheritdoc}
     */
    protected function doToString()
    {
        if ($this->isSeekable()) {
            $offset = $this->tell();
            $this->seek(0);
        }

        $string = (string) $this->stream;

        if ($this->isSeekable()) {
            $this->seek($offset);
        }

        return $string;
    }
}
