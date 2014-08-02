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

/**
 * Request.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Request extends AbstractMessage implements RequestInterface
{
    /** @var string|object */
    protected $url;

    /** @var string */
    protected $method;

    /**
     * Creates a request.
     *
     * @param string|object                          $url             The url.
     * @param string                                 $method          The method.
     * @param array                                  $headers         The headers.
     * @param \Psr\Http\Message\StreamInterface|null $body            The body.
     * @param string                                 $protocolVersion The protocol version.
     */
    public function __construct(
        $url,
        $method = self::METHOD_GET,
        array $headers = array(),
        StreamInterface $body = null,
        $protocolVersion = self::PROTOCOL_VERSION_11
    ) {
        $this->setUrl($url);
        $this->setMethod($method);
        $this->setHeaders($headers);
        $this->setBody($body);
        $this->setProtocolVersion($protocolVersion);
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
        $this->url = $url;
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
        $this->method = strtoupper($method);
    }
}
