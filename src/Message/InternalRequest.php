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
use Psr\Http\Message\StreamableInterface;

/**
 * Internal request.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class InternalRequest extends Request implements InternalRequestInterface
{
    /** @var string */
    private $rawDatas = '';

    /** @var array */
    private $datas = array();

    /** @var array */
    private $files = array();

    /**
     * Creates an internal request.
     *
     * @param string|object $url             The url.
     * @param string        $method          The method.
     * @param string        $protocolVersion The protocol version.
     * @param array         $headers         The headers.
     * @param array|string  $datas           The datas.
     * @param array         $files           The files.
     * @param array         $parameters      The parameters.
     */
    public function __construct(
        $url,
        $method = self::METHOD_GET,
        $protocolVersion = self::PROTOCOL_VERSION_1_1,
        array $headers = array(),
        $datas = array(),
        array $files = array(),
        array $parameters = array()
    ) {
        parent::__construct($url, $method, $protocolVersion, $headers, null, $parameters);

        if (is_array($datas)) {
            $this->setDatas($datas);
        } else {
            $this->setRawDatas($datas);
        }

        $this->setFiles($files);
    }

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
     * @throws \Ivory\HttpAdapter\HttpAdapterException The method is not supported, you should rely to datas/files instead.
     */
    public function getBody()
    {
        throw HttpAdapterException::doesNotSupportBody();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException The method is not supported, you should rely to datas/files instead.
     */
    public function setBody(StreamableInterface $body = null)
    {
        if ($body !== null) {
            throw HttpAdapterException::doesNotSupportBody();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clearRawDatas()
    {
        $this->rawDatas = '';
    }

    /**
     * {@inheritdoc}
     */
    public function hasRawDatas()
    {
        return !empty($this->rawDatas);
    }

    /**
     * {@inheritdoc}
     */
    public function getRawDatas()
    {
        return $this->rawDatas;
    }

    /**
     * {@inheritdoc}
     */
    public function setRawDatas($rawDatas)
    {
        if (!empty($rawDatas)) {
            if ($this->hasDatas()) {
                throw HttpAdapterException::doesNotSupportRawDatasAndDatas();
            }

            if ($this->hasFiles()) {
                throw HttpAdapterException::doesNotSupportRawDatasAndFiles();
            }
        }

        $this->rawDatas = $rawDatas;
    }

    /**
     * {@inheritdoc}
     */
    public function clearDatas()
    {
        $this->datas = array();
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
    public function getDatas()
    {
        return $this->datas;
    }

    /**
     * {@inheritdoc}
     */
    public function setDatas(array $datas)
    {
        if ($this->hasRawDatas()) {
            throw HttpAdapterException::doesNotSupportRawDatasAndDatas();
        }

        $this->clearDatas();
        $this->addDatas($datas);
    }

    /**
     * {@inheritdoc}
     */
    public function addDatas(array $datas)
    {
        foreach ($datas as $name => $value) {
            $this->addData($name, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeDatas(array $names)
    {
        foreach ($names as $name) {
            $this->removeData($name);
        }
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
    public function setData($name, $value)
    {
        if ($this->hasRawDatas()) {
            throw HttpAdapterException::doesNotSupportRawDatasAndFiles();
        }

        $this->removeData($name);
        $this->addData($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function addData($name, $value)
    {
        if ($this->hasRawDatas()) {
            throw HttpAdapterException::doesNotSupportRawDatasAndDatas();
        }

        $this->datas[$name] = $this->hasData($name)
            ? array_merge((array) $this->datas[$name], (array) $value)
            : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function removeData($name)
    {
        unset($this->datas[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function clearFiles()
    {
        $this->files = array();
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
        if ($this->hasRawDatas() && !empty($files)) {
            throw HttpAdapterException::doesNotSupportRawDatasAndFiles();
        }

        $this->clearFiles();
        $this->addFiles($files);
    }

    /**
     * {@inheritdoc}
     */
    public function addFiles(array $files)
    {
        foreach ($files as $name => $file) {
            $this->addFile($name, $file);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeFiles(array $names)
    {
        foreach ($names as $name) {
            $this->removeFile($name);
        }
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
    public function setFile($name, $file)
    {
        if ($this->hasRawDatas()) {
            throw HttpAdapterException::doesNotSupportRawDatasAndFiles();
        }

        $this->removeFile($name);
        $this->addFile($name, $file);
    }

    /**
     * {@inheritdoc}
     */
    public function addFile($name, $file)
    {
        if ($this->hasRawDatas()) {
            throw HttpAdapterException::doesNotSupportRawDatasAndFiles();
        }

        $this->files[$name] = $this->hasFile($name)
            ? array_merge((array) $this->files[$name], (array) $file)
            : $file;
    }

    /**
     * {@inheritdoc}
     */
    public function removeFile($name)
    {
        unset($this->files[$name]);
    }
}
