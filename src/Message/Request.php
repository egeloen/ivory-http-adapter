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

use Ivory\HttpAdapter\Normalizer\MethodNormalizer;
use Ivory\HttpAdapter\Normalizer\UrlNormalizer;

/**
 * Request.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Request extends AbstractMessage implements RequestInterface
{
    /** @var string */
    protected $url;

    /** @var string */
    protected $method;

    /**
     * Creates a request.
     *
     * @param string|object $url    The url.
     * @param string        $method The method.
     */
    public function __construct($url, $method = self::METHOD_GET)
    {
        $this->setProtocolVersion(self::PROTOCOL_VERSION_11);
        $this->setUrl($url);
        $this->setMethod($method);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        $this->url = UrlNormalizer::normalize($url);
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod($method)
    {
        $this->method = MethodNormalizer::normalize($method);
    }
}
