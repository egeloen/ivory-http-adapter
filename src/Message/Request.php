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
use Psr\Http\Message\StreamableInterface;

/**
 * Request.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Request extends AbstractMessage implements RequestInterface
{
    /** @var string */
    private $url;

    /** @var string */
    private $method;

    /**
     * Creates a request.
     *
     * @param string|object                              $url             The url.
     * @param string                                     $method          The method.
     * @param string                                     $protocolVersion The protocol version.
     * @param array                                      $headers         The headers.
     * @param \Psr\Http\Message\StreamableInterface|null $body            The body.
     * @param array                                      $parameters      The parameters.
     */
    public function __construct(
        $url,
        $method = self::METHOD_GET,
        $protocolVersion = self::PROTOCOL_VERSION_1_1,
        array $headers = array(),
        StreamableInterface $body = null,
        array $parameters = array()
    ) {
        parent::__construct($protocolVersion, $headers, $body, $parameters);

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

    /**
     * {@inheritdoc}
     */
    public function setProtocolVersion($protocolVersion)
    {
        parent::setProtocolVersion($protocolVersion);
    }

    /**
     * {@inheritdoc}
     */
    public function setHeaders(array $headers)
    {
        parent::setHeaders($headers);
    }

    /**
     * {@inheritdoc}
     */
    public function addHeaders(array $headers)
    {
        parent::addHeaders($headers);
    }

    /**
     * {@inheritdoc}
     */
    public function removeHeaders($headers)
    {
        parent::removeHeaders($headers);
    }

    /**
     * {@inheritdoc}
     */
    public function setHeader($header, $value)
    {
        parent::setHeader($header, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function addHeader($header, $value)
    {
        parent::addHeader($header, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function removeHeader($header)
    {
        parent::removeHeader($header);
    }

    /**
     * {@inheritdoc}
     */
    public function setBody(StreamableInterface $body = null)
    {
        parent::setBody($body);
    }
}
