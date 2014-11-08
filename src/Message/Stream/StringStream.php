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

/**
 * String stream.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StringStream extends AbstractStream
{
    /** @const integer The seek mode. */
    const MODE_SEEK = 1;

    /** @const integer The read mode. */
    const MODE_READ = 2;

    /** @const integer The write mode. */
    const MODE_WRITE = 4;

    /** @var string */
    private $string;

    /** @var integer */
    private $modeMask;

    /** @var integer */
    private $size;

    /** @var integer */
    private $cursor = 0;

    /**
     * Creates a string stream.
     *
     * @param string|object|null $string   The string.
     * @param integer|null       $modeMask The mode mask.
     */
    public function __construct($string = null, $modeMask = null)
    {
        $this->attach($string, $modeMask);
    }

    /**
     * {@inheritdoc}
     *
     * @param integer|null $modeMask The mode mask.
     */
    public function attach($stream, $modeMask = null)
    {
        $this->detach();
        $this->doAttach($stream, $modeMask);
    }

    /**
     * {@inheritdoc}
     */
    protected function hasValue()
    {
        return is_string($this->string);
    }

    /**
     * {@inheritdoc}
     */
    protected function doClose()
    {
        $this->detach();

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @param integer|null $modeMask The mode mask.
     */
    protected function doAttach($stream, $modeMask = null)
    {
        $this->string = (string) $stream;
        $this->modeMask = $modeMask ?: self::MODE_SEEK | self::MODE_READ | self::MODE_WRITE;
        $this->size = strlen($this->string);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDetach()
    {
        $string = $this->string;

        $this->string = null;
        $this->modeMask = 0;
        $this->size = 0;
        $this->cursor = 0;

        return $string;
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetMetadata($key = null)
    {
        $mode = 'r+';

        if ($this->modeMask & self::MODE_READ && $this->modeMask & self::MODE_WRITE) {
            $mode = 'r+';
        } elseif ($this->modeMask & self::MODE_READ) {
            $mode = 'r';
        } elseif ($this->modeMask & self::MODE_WRITE) {
            $mode = 'a';
        }

        $metadata = array(
            'wrapper_type' => 'data',
            'stream_type'  => 'STDIO',
            'mode'         => $mode,
            'unread_bytes' => 0,
            'seekable'     => $this->isSeekable(),
            'uri'          => 'data://'.$this->string,
            'timed_out'    => false,
            'blocked'      => true,
            'eof'          => $this->eof(),
        );

        if ($key === null) {
            return $metadata;
        }

        return isset($metadata[$key]) ? $metadata[$key] : null;
    }

    /**
     * {@inheritdoc}
     */
    protected function doEof()
    {
        return $this->cursor >= $this->size;
    }

    /**
     * {@inheritdoc}
     */
    protected function doTell()
    {
        return $this->cursor;
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    protected function doIsSeekable()
    {
        return (bool) ($this->modeMask & self::MODE_SEEK);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSeek($offset, $whence)
    {
        $cursor = $this->cursor;

        switch ($whence) {
            case SEEK_SET:
                $cursor = $offset;
                break;

            case SEEK_CUR:
                $cursor += $offset;
                break;

            case SEEK_END:
                $cursor = $this->size + $offset;
                break;
        }

        if ($cursor < 0) {
            return false;
        }

        $this->cursor = $cursor;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doIsReadable()
    {
        return (bool) ($this->modeMask & self::MODE_READ);
    }

    /**
     * {@inheritdoc}
     */
    protected function doRead($length)
    {
        $cursor = $this->cursor;
        $this->forceSeek($this->cursor + $length <= $this->size ? $length : $this->size, SEEK_CUR);

        return substr($this->string, $cursor, $length);
    }

    /**
     * {@inheritdoc}
     */
    protected function doIsWritable()
    {
        return (bool) ($this->modeMask & self::MODE_WRITE);
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite($string)
    {
        if ($this->cursor > $this->size) {
            $this->string .= str_repeat("\0", $this->cursor - $this->size);
        }

        $stringSize = strlen($string);

        if (($newSize = $this->cursor + $stringSize) > $this->size) {
            $this->size = $newSize;
        }

        $cursor = $this->cursor;
        $this->forceSeek($stringSize, SEEK_CUR);
        $this->string = substr_replace($this->string, $string, $cursor, $stringSize);

        return $stringSize;
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetContents()
    {
        $cursor = $this->cursor;
        $this->forceSeek($this->size);

        return substr($this->string, $cursor);
    }

    /**
     * {@inheritdoc}
     */
    protected function doToString()
    {
        return $this->string;
    }

    /**
     * Forces a seek.
     *
     * @param integer $offset The offset.
     * @param integer $whence The whence flag.
     */
    private function forceSeek($offset, $whence = SEEK_SET)
    {
        $modeMask = $this->modeMask;
        $this->modeMask = $modeMask | self::MODE_SEEK;

        $this->seek($offset, $whence);

        $this->modeMask = $modeMask;
    }
}
