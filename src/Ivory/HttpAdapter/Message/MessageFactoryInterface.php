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
     * Creates an internal request.
     *
     * @param string $url    The url.
     * @param string $method The method.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The internal request.
     */
    public function createInternalRequest($url, $method = RequestInterface::METHOD_GET);

    /**
     * Creates a response.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function createResponse();
}
