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

use Ivory\HttpAdapter\Message\InternalRequest;
use Ivory\HttpAdapter\Message\Request;

/**
 * Message factory.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MessageFactory implements MessageFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createRequest($url, $method = Request::METHOD_GET)
    {
        return new Request($url, $method);
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
    public function createInternalRequest($url, $method = Request::METHOD_GET)
    {
        return new InternalRequest($url, $method);
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
    public function createResponse()
    {
        return new Response();
    }

    /**
     * {@inheritdoc}
     */
    public function cloneResponse(ResponseInterface $response)
    {
        return clone $response;
    }
}
