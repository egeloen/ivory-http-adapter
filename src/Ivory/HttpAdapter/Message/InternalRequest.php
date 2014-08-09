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

use Ivory\HttpAdapter\HttpAdapterException;
use Psr\Http\Message\StreamInterface;

/**
 * Internal request.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class InternalRequest extends Request implements InternalRequestInterface
{
    /** @var array|string */
    protected $data = array();

    /** @var array */
    protected $files = array();

    /**
     * {@inheritdoc}
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException The method is not supported, you should rely to data/files instead.
     */
    public function hasBody()
    {
        throw HttpAdapterException::doesNotSupportBody();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException The method is not supported, you should rely to data/files instead.
     */
    public function getBody()
    {
        throw HttpAdapterException::doesNotSupportBody();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException The method is not supported, you should rely to data/files instead.
     */
    public function setBody(StreamInterface $body = null)
    {
        throw HttpAdapterException::doesNotSupportBody();
    }

    /**
     * {@inheritdoc}
     */
    public function hasData()
    {
        return !empty($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function hasStringData()
    {
        return is_string($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function hasArrayData()
    {
        return is_array($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        if (is_string($data) && $this->hasFiles()) {
            throw HttpAdapterException::doesNotSupportDataAsStringAndFiles();
        }

        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFiles()
    {
        return !empty($this->files);
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
    public function setFiles(array $files)
    {
        if ($this->hasStringData() && !empty($files)) {
            throw HttpAdapterException::doesNotSupportDataAsStringAndFiles();
        }

        $this->files = $files;
    }
}
