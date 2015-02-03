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
     * Creates a request.
     *
     * @param string|object                                              $url             The url.
     * @param string                                                     $method          The method.
     * @param string                                                     $protocolVersion The protocol version.
     * @param array                                                      $headers         The headers.
     * @param resource|string|\Psr\Http\Message\StreamableInterface|null $body            The body.
     * @param array                                                      $parameters      The parameters.
     *
     * @return \Ivory\HttpAdapter\Message\RequestInterface The request.
     */
    public function createRequest(
        $url,
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
     * @param string|object $url             The url.
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
        $url,
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
     * @param string                                                     $reasonPhrase    The reason phrase.
     * @param string                                                     $protocolVersion The protocol version.
     * @param array                                                      $headers         The headers.
     * @param resource|string|\Psr\Http\Message\StreamableInterface|null $body            The body.
     * @param array                                                      $parameters      The parameters.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function createResponse(
        $statusCode = 200,
        $reasonPhrase = 'OK',
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

    /**
     * Checks if there is a base url.
     *
     * @return boolean TRUE if there is a base url else FALSE.
     */
    public function hasBaseUrl();

    /**
     * Sets the base url.
     *
     * @param string $baseUrl The base url.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the base url is invalid.
     *
     * @return void No return value.
     */
    public function setBaseUrl($baseUrl);

    /**
     * Gets the base url.
     *
     * @return string The base url.
     */
    public function getBaseUrl();
}
