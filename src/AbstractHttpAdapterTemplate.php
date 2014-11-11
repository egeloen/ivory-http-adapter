<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter;

use Ivory\HttpAdapter\Message\RequestInterface;

/**
 * Abstract http adapter template.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractHttpAdapterTemplate implements HttpAdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($url, array $headers = array())
    {
        return $this->send($url, RequestInterface::METHOD_GET, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function head($url, array $headers = array())
    {
        return $this->send($url, RequestInterface::METHOD_HEAD, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function trace($url, array $headers = array())
    {
        return $this->send($url, RequestInterface::METHOD_TRACE, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function post($url, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->send($url, RequestInterface::METHOD_POST, $headers, $datas, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function put($url, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->send($url, RequestInterface::METHOD_PUT, $headers, $datas, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function patch($url, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->send($url, RequestInterface::METHOD_PATCH, $headers, $datas, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($url, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->send($url, RequestInterface::METHOD_DELETE, $headers, $datas, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function options($url, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->send($url, RequestInterface::METHOD_OPTIONS, $headers, $datas, $files);
    }
}
