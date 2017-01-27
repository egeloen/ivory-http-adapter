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
class MultiRequestCreatedEvent extends AbstractEvent
{
    /**
     * @var InternalRequestInterface[]
     */
    private $requests;

    /**
     * @var ResponseInterface[]
     */
    private $responses = [];

    /**
     * @var HttpAdapterException[]
     */
    private $exceptions = [];

    /**
     * @param HttpAdapterInterface $httpAdapter
     * @param array                $requests
     */
    public function __construct(HttpAdapterInterface $httpAdapter, array $requests)
    {
        parent::__construct($httpAdapter);

        $this->setRequests($requests);
    }

    public function clearRequests()
    {
        $this->requests = [];
    }

    /**
     * @return bool
     */
    public function hasRequests()
    {
        return !empty($this->requests);
    }

    /**
     * @return InternalRequestInterface[]
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * @param InternalRequestInterface[] $requests
     */
    public function setRequests(array $requests)
    {
        $this->clearRequests();
        $this->addRequests($requests);
    }

    /**
     * @param InternalRequestInterface[] $requests
     */
    public function addRequests(array $requests)
    {
        foreach ($requests as $request) {
            $this->addRequest($request);
        }
    }

    /**
     * @param InternalRequestInterface[] $requests
     */
    public function removeRequests(array $requests)
    {
        foreach ($requests as $request) {
            $this->removeRequest($request);
        }
    }

    /**
     * @param InternalRequestInterface $request
     *
     * @return bool
     */
    public function hasRequest(InternalRequestInterface $request)
    {
        return array_search($request, $this->requests, true) !== false;
    }

    /**
     * @param InternalRequestInterface $request
     */
    public function addRequest(InternalRequestInterface $request)
    {
        $this->requests[] = $request;
    }

    /**
     * @param InternalRequestInterface $request
     */
    public function removeRequest(InternalRequestInterface $request)
    {
        unset($this->requests[array_search($request, $this->requests, true)]);
        $this->requests = array_values($this->requests);
    }

    public function clearResponses()
    {
        $this->responses = [];
    }

    /**
     * @return bool
     */
    public function hasResponses()
    {
        return !empty($this->responses);
    }

    /**
     * @return ResponseInterface[]
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * @param ResponseInterface[] $responses
     */
    public function setResponses(array $responses)
    {
        $this->clearResponses();
        $this->addResponses($responses);
    }

    /**
     * @param ResponseInterface[] $responses
     */
    public function addResponses(array $responses)
    {
        foreach ($responses as $response) {
            $this->addResponse($response);
        }
    }

    /**
     * @param ResponseInterface[] $responses
     */
    public function removeResponses(array $responses)
    {
        foreach ($responses as $response) {
            $this->removeResponse($response);
        }
    }

    /**
     * @param ResponseInterface $response
     *
     * @return bool
     */
    public function hasResponse(ResponseInterface $response)
    {
        return array_search($response, $this->responses, true) !== false;
    }

    /**
     * @param ResponseInterface $response
     */
    public function addResponse(ResponseInterface $response)
    {
        $this->responses[] = $response;
    }

    /**
     * @param ResponseInterface $response
     */
    public function removeResponse(ResponseInterface $response)
    {
        unset($this->responses[array_search($response, $this->responses, true)]);
        $this->responses = array_values($this->responses);
    }

    public function clearExceptions()
    {
        $this->exceptions = [];
    }

    /**
     * @return bool
     */
    public function hasExceptions()
    {
        return !empty($this->exceptions);
    }

    /**
     * @return HttpAdapterException[]
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * @param HttpAdapterException[] $exceptions
     */
    public function setExceptions(array $exceptions)
    {
        $this->clearExceptions();
        $this->addExceptions($exceptions);
    }

    /**
     * @param HttpAdapterException[] $exceptions
     */
    public function addExceptions(array $exceptions)
    {
        foreach ($exceptions as $exception) {
            $this->addException($exception);
        }
    }

    /**
     * @param HttpAdapterException[] $exceptions
     */
    public function removeExceptions(array $exceptions)
    {
        foreach ($exceptions as $exception) {
            $this->removeException($exception);
        }
    }

    /**
     * @param HttpAdapterException $exception
     *
     * @return bool
     */
    public function hasException(HttpAdapterException $exception)
    {
        return array_search($exception, $this->exceptions, true) !== false;
    }

    /**
     * @param HttpAdapterException $exception
     */
    public function addException(HttpAdapterException $exception)
    {
        $this->exceptions[] = $exception;
    }

    /**
     * @param HttpAdapterException $exception
     */
    public function removeException(HttpAdapterException $exception)
    {
        unset($this->exceptions[array_search($exception, $this->exceptions, true)]);
        $this->exceptions = array_values($this->exceptions);
    }
}
