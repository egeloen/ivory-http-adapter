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

use Ivory\HttpAdapter\Normalizer\HeadersNormalizer;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Uri;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class MessageFactory implements MessageFactoryInterface
{
    /**
     * @var Uri|null
     */
    private $baseUri;

    /**
     * @param string $baseUri
     */
    public function __construct($baseUri = null)
    {
        $this->setBaseUri($baseUri);
    }

    /**
     * {@inheritdoc}
     */
    public function hasBaseUri()
    {
        return $this->baseUri !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseUri($baseUri = null)
    {
        if (is_string($baseUri)) {
            $baseUri = new Uri($baseUri);
        }

        $this->baseUri = $baseUri;
    }

    /**
     * {@inheritdoc}
     */
    public function createRequest(
        $uri,
        $method = RequestInterface::METHOD_GET,
        $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_1,
        array $headers = [],
        $body = null,
        array $parameters = []
    ) {
        return (new Request(
            $this->createUri($uri),
            $method,
            $this->createStream($body),
            HeadersNormalizer::normalize($headers),
            $parameters
        ))->withProtocolVersion($protocolVersion);
    }

    /**
     * {@inheritdoc}
     */
    public function createInternalRequest(
        $uri,
        $method = RequestInterface::METHOD_GET,
        $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_1,
        array $headers = [],
        $datas = [],
        array $files = [],
        array $parameters = []
    ) {
        $body = null;

        if (!is_array($datas)) {
            $body = $this->createStream($datas);
            $datas = $files = [];
        }

        return (new InternalRequest(
            $this->createUri($uri),
            $method,
            $body !== null ? $body : 'php://memory',
            $datas,
            $files,
            HeadersNormalizer::normalize($headers),
            $parameters
        ))->withProtocolVersion($protocolVersion);
    }

    /**
     * {@inheritdoc}
     */
    public function createResponse(
        $statusCode = 200,
        $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_1,
        array $headers = [],
        $body = null,
        array $parameters = []
    ) {
        return (new Response(
            $this->createStream($body),
            $statusCode,
            HeadersNormalizer::normalize($headers),
            $parameters
        ))->withProtocolVersion($protocolVersion);
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    private function createUri($uri)
    {
        if ($this->hasBaseUri() && (stripos($uri, $baseUri = (string) $this->getBaseUri()) === false)) {
            return $baseUri.$uri;
        }

        return $uri;
    }

    /**
     * @param resource|string|StreamInterface|null $body
     *
     * @return StreamInterface
     */
    private function createStream($body)
    {
        if ($body instanceof StreamInterface) {
            $body->rewind();

            return $body;
        }

        if (is_resource($body)) {
            return $this->createStream(new Stream($body));
        }

        $stream = new Stream('php://memory', 'rw');

        if ($body === null) {
            return $stream;
        }

        $stream->write((string) $body);

        return $this->createStream($stream);
    }
}
