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

use Guzzle\Stream\StreamInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Psr\Http\Message\UriInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface MessageFactoryInterface
{
    /**
     * @return bool
     */
    public function hasBaseUri();

    /**
     * @return UriInterface|null
     */
    public function getBaseUri();

    /**
     * @param string|UriInterface|null $baseUri
     *
     * @throws HttpAdapterException
     */
    public function setBaseUri($baseUri);

    /**
     * @param string|object                        $uri
     * @param string                               $method
     * @param string                               $protocolVersion
     * @param array                                $headers
     * @param resource|string|StreamInterface|null $body
     * @param array                                $parameters
     *
     * @return RequestInterface
     */
    public function createRequest(
        $uri,
        $method = RequestInterface::METHOD_GET,
        $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_1,
        array $headers = [],
        $body = null,
        array $parameters = []
    );

    /**
     * @param string|object $uri
     * @param string        $method
     * @param string        $protocolVersion
     * @param array         $headers
     * @param array|string  $datas
     * @param array         $files
     * @param array         $parameters
     *
     * @return InternalRequestInterface
     */
    public function createInternalRequest(
        $uri,
        $method = RequestInterface::METHOD_GET,
        $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_1,
        array $headers = [],
        $datas = [],
        array $files = [],
        array $parameters = []
    );

    /**
     * @param int                                  $statusCode
     * @param string                               $protocolVersion
     * @param array                                $headers
     * @param resource|string|StreamInterface|null $body
     * @param array                                $parameters
     *
     * @return ResponseInterface
     */
    public function createResponse(
        $statusCode = 200,
        $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_1,
        array $headers = [],
        $body = null,
        array $parameters = []
    );
}
