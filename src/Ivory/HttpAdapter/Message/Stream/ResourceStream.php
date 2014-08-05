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

use Ivory\HttpAdapter\HttpAdapterException;

/**
 * Resource stream.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceStream extends AbstractStream
{
    /** @var string */
    protected static $isReadable = 'is_readable';

    /** @var string */
    protected static $isWritable = 'is_writable';

    /** @var string */
    protected static $isSeekable = 'seekable';

    /** @var string */
    protected static $isLocal = 'is_local';

    /** @var array */
    protected static $modes = array(
        'read' => array(
            'r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true, 'c+b' => true,
            'rt' => true, 'w+t' => true, 'r+t' => true, 'x+t' => true, 'c+t' => true, 'a+' => true
        ),
        'write' => array(
            'w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'wb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true, 'c+b' => true,
            'w+t' => true, 'r+t' => true, 'x+t' => true, 'c+t' => true, 'a' => true, 'a+' => true
        )
    );

    /** @var resource */
    protected $resource;

    /** @var array */
    protected $cache;

    /** @var integer|null */
    protected $size;

    /**
     * Creates a resource stream.
     *
     * @param resource $resource The resource.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the resource is not valid.
     */
    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            throw HttpAdapterException::resourceIsNotValid($resource);
        }

        $this->resource = $resource;
        $this->buildCache();
    }

    /**
     * {@inheritdoc}
     */
    protected function hasValue()
    {
        return is_resource($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    protected function doClose()
    {
        $this->clearCache();

        return fclose($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDetach()
    {
        $this->clearCache();
        $this->resource = null;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doEof()
    {
        return feof($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    protected function doTell()
    {
        return ftell($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetSize()
    {
        if ($this->size !== null) {
            return $this->size;
        }

        if ($this->isLocal()) {
            clearstatcache(true, $this->cache['uri']);
            $stats = fstat($this->resource);

            return $this->size = $stats['size'];
        }

        return $this->size = strlen((string) $this);
    }

    /**
     * {@inheritdoc}
     */
    protected function doIsSeekable()
    {
        return $this->cache[self::$isSeekable];
    }

    /**
     * {@inheritdoc}
     */
    protected function doSeek($offset, $whence)
    {
        return fseek($this->resource, $offset, $whence) === 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function doIsReadable()
    {
        return $this->cache[self::$isReadable];
    }

    /**
     * {@inheritdoc}
     */
    protected function doRead($length)
    {
        return fread($this->resource, $length);
    }

    /**
     * {@inheritdoc}
     */
    protected function doIsWritable()
    {
        return $this->cache[self::$isWritable];
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite($string)
    {
        $bytes = fwrite($this->resource, $string);

        if ($bytes) {
            $this->size = null;
        }

        return $bytes;
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetContents($maxLength)
    {
        return stream_get_contents($this->resource, $maxLength);
    }

    /**
     * {@inheritdoc}
     */
    protected function doToString()
    {
        if ($this->isSeekable()) {
            $cursor = $this->tell();
            $this->seek(0);
        }

        $content = stream_get_contents($this->resource);

        if ($this->isSeekable()) {
            $this->seek($cursor);
        }

        return $content;
    }

    /**
     * Checks if the stream is local.
     *
     * @return boolean TRUE if the stream is local else FALSE.
     */
    public function isLocal()
    {
        return $this->cache[self::$isLocal];
    }

    /**
     * Builds the cache.
     */
    protected function buildCache()
    {
        $this->cache = stream_get_meta_data($this->resource);
        $this->cache[self::$isLocal] = stream_is_local($this->resource);
        $this->cache[self::$isReadable] = isset(self::$modes['read'][$this->cache['mode']]);
        $this->cache[self::$isWritable] = isset(self::$modes['write'][$this->cache['mode']]);
    }

    /**
     * Clears the cache.
     */
    protected function clearCache()
    {
        $this->cache[self::$isReadable] = false;
        $this->cache[self::$isWritable] = false;
        $this->cache[self::$isSeekable] = false;
        $this->cache[self::$isLocal] = false;
    }
}
