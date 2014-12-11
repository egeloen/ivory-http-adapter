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
    /** @var array */
    private static $modes = array(
        'read' => array(
            'r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true, 'rb' => true,
            'w+b' => true, 'r+b' => true, 'x+b' => true, 'c+b' => true, 'rt' => true,
            'w+t' => true, 'r+t' => true, 'x+t' => true, 'c+t' => true, 'a+' => true,
        ),
        'write' => array(
            'w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'wb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true, 'c+b' => true, 'w+t' => true,
            'r+t' => true, 'x+t' => true, 'c+t' => true, 'a' => true, 'a+' => true,
        ),
    );

    /** @var resource */
    private $resource;

    /** @var array */
    private $cache;

    /** @var integer|null */
    private $size;

    /**
     * Creates a resource stream.
     *
     * @param resource $resource The resource.
     */
    public function __construct($resource)
    {
        $this->attach($resource);
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
        $resource = $this->resource;
        $this->detach();

        return fclose($resource);
    }

    /**
     * {@inheritdoc}
     */
    protected function doAttach($stream)
    {
        if (!is_resource($stream)) {
            throw HttpAdapterException::streamIsNotValid($stream, get_class($this), 'resource');
        }

        $this->resource = $stream;

        $metadata = $this->getMetadata();

        $this->cache['readable'] = isset(self::$modes['read'][$metadata['mode']]);
        $this->cache['writable'] = isset(self::$modes['write'][$metadata['mode']]);
        $this->cache['seekable'] = $metadata['seekable'];
        $this->cache['local'] = stream_is_local($this->resource);
        $this->cache['uri'] = $metadata['uri'];
    }

    /**
     * {@inheritdoc}
     */
    protected function doDetach()
    {
        $resource = $this->resource;

        $this->resource = null;

        $this->cache['readable'] = false;
        $this->cache['writable'] = false;
        $this->cache['seekable'] = false;
        $this->cache['local'] = false;
        unset($this->cache['uri']);

        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetMetadata($key = null)
    {
        $metadata = stream_get_meta_data($this->resource);

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

        if ($this->cache['local']) {
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
        return $this->cache['seekable'];
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
        return $this->cache['readable'];
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
        return $this->cache['writable'];
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
    protected function doGetContents()
    {
        return stream_get_contents($this->resource);
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
}
