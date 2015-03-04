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

/**
 * Message factory interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface MessageFactoryInterface
{
    /**
     * Checks if there is a base uri.
     *
     * @return boolean TRUE if there is a base uri else FALSE.
     */
    public function hasBaseUri();

    /**
     * Gets the base uri.
     *
     * @return null|\Psr\Http\Message\UriInterface The base uri.
     */
    public function getBaseUri();

    /**
     * Sets the base uri.
     *
     * @param null|string|\Psr\Http\Message\UriInterface $baseUri The base uri.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the base uri is invalid.
     */
    public function setBaseUri($baseUri);

    /**
     * Creates a request.
     *
     * @param string|object                                              $uri             The uri.
     * @param string                                                     $method          The method.
     * @param string                                                     $protocolVersion The protocol version.
     * @param array                                                      $headers         The headers.
     * @param resource|string|\Psr\Http\Message\StreamableInterface|null $body            The body.
     * @param array                                                      $parameters      The parameters.
     *
     * @return \Ivory\HttpAdapter\Message\RequestInterface The request.
     */
    public function createRequest(
        $uri,
        $method = RequestInterface::METHOD_GET,
        $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_1,
        array $headers = array(),
        $body = null,
        array $parameters = array()
    );

    /**
     * Clones a request.
     *
     * @param \Ivory\HttpAdapter\Message\RequestInterface $request The request.
     *
     * @return \Ivory\HttpAdapter\Message\RequestInterface The cloned request.
     */
    public function cloneRequest(RequestInterface $request);

    /**
     * Creates an internal request.
     *
     * @param string|object $uri             The uri.
     * @param string        $method          The method.
     * @param string        $protocolVersion The protocol version.
     * @param array         $headers         The headers.
     * @param array|string  $datas           The datas.
     * @param array         $files           The files.
     * @param array         $parameters      The parameters.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The internal request.
     */
    public function createInternalRequest(
        $uri,
        $method = RequestInterface::METHOD_GET,
        $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_1,
        array $headers = array(),
        $datas = array(),
        array $files = array(),
        array $parameters = array()
    );

    /**
     * Clones an internal request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The cloned internal request.
     */
    public function cloneInternalRequest(InternalRequestInterface $internalRequest);

    /**
     * Creates a response.
     *
     * @param integer                                                    $statusCode      The status code.
     * @param string                                                     $protocolVersion The protocol version.
     * @param array                                                      $headers         The headers.
     * @param resource|string|\Psr\Http\Message\StreamableInterface|null $body            The body.
     * @param array                                                      $parameters      The parameters.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function createResponse(
        $statusCode = 200,
        $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_1,
        array $headers = array(),
        $body = null,
        array $parameters = array()
    );

    /**
     * Clones a response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface $response The response.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The cloned response.
     */
    public function cloneResponse(ResponseInterface $response);
}
