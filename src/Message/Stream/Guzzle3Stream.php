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

use Guzzle\Stream\StreamInterface;

/**
 * Guzzle 3 stream.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Guzzle3Stream extends AbstractStream
{
    /** @var \Guzzle\Stream\StreamInterface */
    protected $stream;

    /**
     * Creates a guzzle stream.
     *
     * @param \Guzzle\Stream\StreamInterface $stream
     */
    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    /**
     * {@inheritdoc}
     */
    protected function hasValue()
    {
        return is_resource($this->stream->getStream());
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
    protected function doDetach()
    {
        $this->stream->detachStream();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doEof()
    {
        return $this->stream->feof();
    }

    /**
     * {@inheritdoc}
     */
    protected function doTell()
    {
        return $this->stream->ftell();
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
    protected function doGetContents($maxLength = -1)
    {
        return stream_get_contents($this->stream->getStream(), $maxLength);
    }

    /**
     * {@inheritdoc}
     */
    protected function doToString()
    {
        return (string) $this->stream;
    }
}
