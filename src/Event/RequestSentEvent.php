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

use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * Request sent event.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RequestSentEvent extends AbstractEvent
{
    /** @var \Ivory\HttpAdapter\Message\InternalRequestInterface */
    private $request;

    /** @var \Ivory\HttpAdapter\Message\ResponseInterface|null */
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
        parent::__construct($httpAdapter);

        $this->setRequest($request);
        $this->setResponse($response);
    }

    /**
     * Gets the request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The request.
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets the request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     */
    public function setRequest(InternalRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Gets the response.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets the response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface|null $response
     */
    public function setResponse(ResponseInterface $response = null)
    {
        $this->response = $response;
    }

    /**
     * Checks if there is an exception.
     *
     * @return boolean TRUE if there is an exception else FALSE.
     */
    public function hasException()
    {
        return $this->exception !== null;
    }

    /**
     * Gets the exception.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException|null
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Sets the exception.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterException|null $exception
     */
    public function setException(HttpAdapterException $exception = null)
    {
        $this->exception = $exception;
    }
}
