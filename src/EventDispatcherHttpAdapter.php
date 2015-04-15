<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter;

use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\RequestErroredEvent;
use Ivory\HttpAdapter\Event\MultiRequestErroredEvent;
use Ivory\HttpAdapter\Event\MultiRequestSentEvent;
use Ivory\HttpAdapter\Event\MultiRequestCreatedEvent;
use Ivory\HttpAdapter\Event\RequestSentEvent;
use Ivory\HttpAdapter\Event\RequestCreatedEvent;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Event dispatcher http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class EventDispatcherHttpAdapter extends PsrHttpAdapterDecorator
{
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * @param \Ivory\HttpAdapter\PsrHttpAdapterInterface                  $httpAdapter
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(PsrHttpAdapterInterface $httpAdapter, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($httpAdapter);

        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSendInternalRequest(InternalRequestInterface $internalRequest)
    {
        try {
            $this->eventDispatcher->dispatch(
                Events::REQUEST_CREATED,
                $requestCreatedEvent = new RequestCreatedEvent($this, $internalRequest)
            );

            $response = parent::doSendInternalRequest($requestCreatedEvent->getRequest());

            $this->eventDispatcher->dispatch(
                Events::REQUEST_SENT,
                $requestSentEvent = new RequestSentEvent($this, $requestCreatedEvent->getRequest(), $response)
            );

            if ($requestSentEvent->hasException()) {
                throw $requestSentEvent->getException();
            }

            $response = $requestSentEvent->getResponse();
        } catch (HttpAdapterException $e) {
            $e->setRequest($internalRequest);
            $e->setResponse(isset($response) ? $response : null);

            $this->eventDispatcher->dispatch(
                Events::REQUEST_ERRORED,
                $exceptionEvent = new RequestErroredEvent($this, $e)
            );

            if ($exceptionEvent->hasResponse()) {
                return $exceptionEvent->getResponse();
            }

            throw $exceptionEvent->getException();
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSendInternalRequests(array $internalRequests)
    {
        if (!empty($internalRequests)) {
            $this->eventDispatcher->dispatch(
                Events::MULTI_REQUEST_CREATED,
                $multiRequestCreatedEvent = new MultiRequestCreatedEvent($this, $internalRequests)
            );

            $internalRequests = $multiRequestCreatedEvent->getRequests();
        }

        $exceptions = array();

        try {
            $responses = parent::doSendInternalRequests($internalRequests);
        } catch (MultiHttpAdapterException $e) {
            $responses = $e->getResponses();
            $exceptions = $e->getExceptions();
        }

        if (!empty($responses)) {
            $this->eventDispatcher->dispatch(
                Events::MULTI_REQUEST_SENT,
                $requestSentEvent = new MultiRequestSentEvent($this, $responses)
            );

            $exceptions = array_merge($exceptions, $requestSentEvent->getExceptions());
            $responses = $requestSentEvent->getResponses();
        }

        if (!empty($exceptions)) {
            $this->eventDispatcher->dispatch(
                Events::MULTI_REQUEST_ERRORED,
                $exceptionEvent = new MultiRequestErroredEvent($this, $exceptions)
            );

            $responses = array_merge($responses, $exceptionEvent->getResponses());
            $exceptions = $exceptionEvent->getExceptions();

            if (!empty($exceptions)) {
                throw new MultiHttpAdapterException($exceptions, $responses);
            }
        }

        return $responses;
    }
}
