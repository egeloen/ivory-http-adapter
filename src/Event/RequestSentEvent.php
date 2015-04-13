<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event;

use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\HttpAdapter\HttpAdapterException;

/**
 * Request sent event.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RequestSentEvent extends RequestCreatedEvent
{
    /** @var \Ivory\HttpAdapter\Message\ResponseInterface */
    private $response;

    /** @var \Ivory\HttpAdapter\HttpAdapterException|null */
    private $exception;

    /**
     * Creates a request sent event.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface             $httpAdapter The http adapter.
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request     The request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response    The response.
     */
    public function __construct(
        HttpAdapterInterface $httpAdapter,
        InternalRequestInterface $request,
        ResponseInterface $response
    ) {
        parent::__construct($httpAdapter, $request);

        $this->setResponse($response);
    }

    /**
     * Gets the response.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets the response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface $response The response.
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Checks if there is an exception.
     *
     * @return boolean TRUE if there is an exception.
     */
    public function hasException()
    {
        return $this->exception !== null;
    }

    /**
     * Gets the exception.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException|null The exception.
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Sets the exception.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterException|null $exception The exception.
     */
    public function setException(HttpAdapterException $exception = null)
    {
        $this->exception = $exception;
    }
}
