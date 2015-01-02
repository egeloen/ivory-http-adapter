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

use Ivory\HttpAdapter\Message\Stream\ResourceStream;
use Ivory\HttpAdapter\Message\Stream\StringStream;
use Ivory\HttpAdapter\Normalizer\UrlNormalizer;

/**
 * Message factory.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MessageFactory implements MessageFactoryInterface
{
    /** @var string */
    private $baseUrl;

    /**
     * {@inheritdoc}
     */
    public function createRequest(
        $url,
        $method = RequestInterface::METHOD_GET,
        $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_1,
        array $headers = array(),
        $body = null,
        array $parameters = array()
    ) {
        return new Request(
            $this->prepareUrl($url),
            $method,
            $protocolVersion,
            $headers,
            $this->createStream($body),
            $parameters
        );
    }

    /**
     * {@inheritdoc}
     */
    public function cloneRequest(RequestInterface $request)
    {
        return clone $request;
    }

    /**
     * {@inheritdoc}
     */
    public function createInternalRequest(
        $url,
        $method = RequestInterface::METHOD_GET,
        $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_1,
        array $headers = array(),
        $datas = array(),
        array $files = array(),
        array $parameters = array()
    ) {
        return new InternalRequest(
            $this->prepareUrl($url),
            $method,
            $protocolVersion,
            $headers,
            $datas,
            $files,
            $parameters
        );
    }

    /**
     * {@inheritdoc}
     */
    public function cloneInternalRequest(InternalRequestInterface $internalRequest)
    {
        return clone $internalRequest;
    }

    /**
     * {@inheritdoc}
     */
    public function hasBaseUrl()
    {
        return $this->baseUrl !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = UrlNormalizer::normalize($baseUrl);
    }

    /**
     * Prepares an url.
     *
     * @param  string $url The url.
     * @return string The prepared url.
     */
    private function prepareUrl($url)
    {
        if ($this->hasBaseUrl() && false === stripos($url, $baseUrl = $this->getBaseUrl())) {
            $url = $baseUrl.$url;
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function createResponse(
        $statusCode = 200,
        $reasonPhrase = 'OK',
        $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_1,
        array $headers = array(),
        $body = null,
        array $parameters = array()
    ) {
        return new Response(
            $statusCode,
            $reasonPhrase,
            $protocolVersion,
            $headers,
            $this->createStream($body),
            $parameters
        );
    }

    /**
     * {@inheritdoc}
     */
    public function cloneResponse(ResponseInterface $response)
    {
        return clone $response;
    }

    /**
     * Creates a stream.
     *
     * @param resource|string|\Psr\Http\Message\StreamableInterface|null $body The body.
     *
     * @return \Psr\Http\Message\StreamableInterface|null The stream.
     */
    private function createStream($body)
    {
        if (is_resource($body)) {
            return new ResourceStream($body);
        }

        if (is_string($body)) {
            return new StringStream($body, StringStream::MODE_SEEK | StringStream::MODE_READ);
        }

        return $body;
    }
}
