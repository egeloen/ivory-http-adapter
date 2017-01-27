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

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class InternalRequest extends Request implements InternalRequestInterface
{
    /**
     * @var array
     */
    private $datas = [];

    /**
     * @var array
     */
    private $files = [];

    /**
     * @param null|string|UriInterface        $uri
     * @param null|string                     $method
     * @param string|resource|StreamInterface $body
     * @param array                           $datas
     * @param array                           $files
     * @param array                           $headers
     * @param array                           $parameters
     */
    public function __construct(
        $uri = null,
        $method = null,
        $body = 'php://memory',
        array $datas = [],
        array $files = [],
        array $headers = [],
        array $parameters = []
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
