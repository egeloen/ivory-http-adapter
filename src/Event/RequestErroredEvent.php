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
 * @author GeLo <geloen.eric@gmail.com>
 */
class RequestErroredEvent extends AbstractEvent
{
    /**
     * @var HttpAdapterException
     */
    private $exception;

    /**
     * @var ResponseInterface|null
     */
    private $response;

    /**
     * @param HttpAdapterInterface $httpAdapter
     * @param HttpAdapterException $exception
     */
    public function __construct(HttpAdapterInterface $httpAdapter, HttpAdapterException $exception)
    {
        parent::__construct($httpAdapter);

        $this->setException($exception);
    }

    /**
     * @return HttpAdapterException
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param HttpAdapterException $exception
     */
    public function setException(HttpAdapterException $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return bool
     */
    public function hasResponse()
    {
        return $this->response !== null;
    }

    /**
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }
}
