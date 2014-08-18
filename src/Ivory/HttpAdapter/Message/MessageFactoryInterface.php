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

use Ivory\HttpAdapter\Message\RequestInterface;

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
     * @param string $url    The url.
     * @param string $method The method.
     *
     * @return \Ivory\HttpAdapter\Message\RequestInterface The request.
     */
    public function createRequest($url, $method = RequestInterface::METHOD_GET);

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
     * @param string $url    The url.
     * @param string $method The method.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The internal request.
     */
    public function createInternalRequest($url, $method = RequestInterface::METHOD_GET);

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
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function createResponse();

    /**
     * Clones a response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface $response The response.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The cloned response.
     */
    public function cloneResponse(ResponseInterface $response);
}
