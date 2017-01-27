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
 * @author GeLo <geloen.eric@gmail.com>
 */
class RequestCreatedEvent extends AbstractEvent
{
    /**
     * @var InternalRequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface|null
     */
    private $response;

    /**
     * @var HttpAdapterException|null
     */
    private $exception;

    /**
     * @param HttpAdapterInterface     $httpAdapter
     * @param InternalRequestInterface $request
     */
    public function __construct(HttpAdapterInterface $httpAdapter, InternalRequestInterface $request)
    {
        parent::__construct($httpAdapter);

        $this->setRequest($request);
    }

    /**
     * @return InternalRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param InternalRequestInterface $request
     */
    public function setRequest(InternalRequestInterface $request)
    {
        $this->request = $request;
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
     * @param ResponseInterface|null $response
     */
    public function setResponse(ResponseInterface $response = null)
    {
        $this->response = $response;
    }

    /**
     * @return bool
     */
    public function hasException()
    {
        return $this->exception !== null;
    }

    /**
     * @return HttpAdapterException|null
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param HttpAdapterException|null $exception
     */
    public function setException(HttpAdapterException $exception = null)
    {
        $this->exception = $exception;
    }
}
