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
    public function get($uri, array $headers = array())
    {
        return $this->send($uri, RequestInterface::METHOD_GET, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function head($uri, array $headers = array())
    {
        return $this->send($uri, RequestInterface::METHOD_HEAD, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function trace($uri, array $headers = array())
    {
        return $this->send($uri, RequestInterface::METHOD_TRACE, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function post($uri, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->send($uri, RequestInterface::METHOD_POST, $headers, $datas, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function put($uri, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->send($uri, RequestInterface::METHOD_PUT, $headers, $datas, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function patch($uri, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->send($uri, RequestInterface::METHOD_PATCH, $headers, $datas, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($uri, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->send($uri, RequestInterface::METHOD_DELETE, $headers, $datas, $files);
    }

    /**
     * {@inheritdoc}
     */
    public function options($uri, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->send($uri, RequestInterface::METHOD_OPTIONS, $headers, $datas, $files);
    }
}
