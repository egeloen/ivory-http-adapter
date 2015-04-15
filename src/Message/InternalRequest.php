<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Message;

/**
 * Internal request.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class InternalRequest extends Request implements InternalRequestInterface
{
    /** @var array */
    private $datas = array();

    /** @var array */
    private $files = array();

    /**
     * @param null|string|\Psr\Http\Message\UriInterface        $uri        The internal request uri.
     * @param null|string                                       $method     The internal request method.
     * @param string|resource|\Psr\Http\Message\StreamInterface $body       The internal request body.
     * @param array                                             $datas      The internal request datas.
     * @param array                                             $files      The internal request files.
     * @param array                                             $headers    The internal request headers.
     * @param array                                             $parameters The internal request parameters.
     */
    public function __construct(
        $uri = null,
        $method = null,
        $body = 'php://memory',
        array $datas = array(),
        array $files = array(),
        array $headers = array(),
        array $parameters = array()
    ) {
        parent::__construct($uri, $method, $body, $headers, $parameters);

        $this->datas = $datas;
        $this->files = $files;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatas()
    {
        return $this->datas;
    }

    /**
     * {@inheritdoc}
     */
    public function hasData($name)
    {
        return isset($this->datas[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getData($name)
    {
        return $this->hasData($name) ? $this->datas[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function withData($name, $value)
    {
        $new = clone $this;
        $new->datas[$name] = $value;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedData($name, $value)
    {
        $new = clone $this;
        $new->datas[$name] = $new->hasData($name)
            ? array_merge((array) $new->datas[$name], (array) $value)
            : $value;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutData($name)
    {
        $new = clone $this;
        unset($new->datas[$name]);

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFile($name)
    {
        return isset($this->files[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFile($name)
    {
        return $this->hasFile($name) ? $this->files[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function withFile($name, $file)
    {
        $new = clone $this;
        $new->files[$name] = $file;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedFile($name, $file)
    {
        $new = clone $this;
        $new->files[$name] = $new->hasFile($name)
            ? array_merge((array) $new->files[$name], (array) $file)
            : $file;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutFile($name)
    {
        $new = clone $this;
        unset($new->files[$name]);

        return $new;
    }
}
