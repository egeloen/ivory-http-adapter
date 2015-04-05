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
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * Request errored event.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RequestErroredEvent extends AbstractEvent
{
    /** @var \Ivory\HttpAdapter\HttpAdapterException */
    private $exception;

    /** @var \Ivory\HttpAdapter\Message\ResponseInterface|null */
    private $response;

    /**
     * Creates a request errored event.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface $httpAdapter The http adapter.
     * @param \Ivory\HttpAdapter\HttpAdapterException $exception   The exception.
     */
    public function __construct(HttpAdapterInterface $httpAdapter, HttpAdapterException $exception)
    {
        parent::__construct($httpAdapter);

        $this->setException($exception);
    }

    /**
     * Gets the exception.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The exception.
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Sets the exception.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterException $exception The exception.
     */
    public function setException(HttpAdapterException $exception)
    {
        $this->exception = $exception;
    }

    /**
     * Checks if there is a response.
     *
     * @return boolean TRUE if there is a response else FALSE.
     */
    public function hasResponse()
    {
        return $this->response !== null;
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
}
