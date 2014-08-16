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
    protected $datas = array();

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
    public function hasDatas()
    {
        return !empty($this->datas);
    }

    /**
     * {@inheritdoc}
     */
    public function hasStringDatas()
    {
        return is_string($this->datas);
    }

    /**
     * {@inheritdoc}
     */
    public function hasArrayDatas()
    {
        return is_array($this->datas);
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
    public function setDatas($datas)
    {
        if (is_string($datas) && $this->hasFiles()) {
            throw HttpAdapterException::doesNotSupportDatasAsStringAndFiles();
        }

        $this->datas = $datas;
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
        if ($this->hasStringDatas() && !empty($files)) {
            throw HttpAdapterException::doesNotSupportDatasAsStringAndFiles();
        }

        $this->files = $files;
    }
}
