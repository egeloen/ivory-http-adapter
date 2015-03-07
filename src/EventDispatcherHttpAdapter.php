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
use Ivory\HttpAdapter\Event\ExceptionEvent;
use Ivory\HttpAdapter\Event\MultiExceptionEvent;
use Ivory\HttpAdapter\Event\MultiPostSendEvent;
use Ivory\HttpAdapter\Event\MultiPreSendEvent;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\PreSendEvent;
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
    protected function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        try {
            $this->eventDispatcher->dispatch(
                Events::PRE_SEND,
                $preSendEvent = new PreSendEvent($this, $internalRequest)
            );

            $response = parent::sendInternalRequest($preSendEvent->getRequest());

            $this->eventDispatcher->dispatch(
                Events::POST_SEND,
                $postSendEvent = new PostSendEvent($this, $preSendEvent->getRequest(), $response)
            );

            if ($postSendEvent->hasException()) {
                throw $postSendEvent->getException();
            }

            $response = $postSendEvent->getResponse();
        } catch (HttpAdapterException $e) {
            $e->setRequest($internalRequest);
            $e->setResponse(isset($response) ? $response : null);

            $this->eventDispatcher->dispatch(
                Events::EXCEPTION,
                $exceptionEvent = new ExceptionEvent($this, $e)
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
    protected function sendInternalRequests(array $internalRequests, $success, $error)
    {
        if (!empty($internalRequests)) {
            $this->eventDispatcher->dispatch(
                Events::MULTI_PRE_SEND,
                $multiPreSendEvent = new MultiPreSendEvent($this, $internalRequests)
            );

            $internalRequests = $multiPreSendEvent->getRequests();
        }

        $exceptions = array();

        try {
            $responses = $this->decorate('sendRequests', array($internalRequests));
        } catch (MultiHttpAdapterException $e) {
            $responses = $e->getResponses();
            $exceptions = $e->getExceptions();
        }

        if (!empty($responses)) {
            $this->eventDispatcher->dispatch(
                Events::MULTI_POST_SEND,
                $postSendEvent = new MultiPostSendEvent($this, $responses)
            );

            $exceptions = array_merge($exceptions, $postSendEvent->getExceptions());
            $responses = $postSendEvent->getResponses();
        }

        if (!empty($exceptions)) {
            $this->eventDispatcher->dispatch(
                Events::MULTI_EXCEPTION,
                $exceptionEvent = new MultiExceptionEvent($this, $exceptions)
            );

            $responses = array_merge($responses, $exceptionEvent->getResponses());
            $exceptions = $exceptionEvent->getExceptions();
        }

        foreach ($responses as $response) {
            call_user_func($success, $response);
        }

        foreach ($exceptions as $exception) {
            call_user_func($error, $exception);
        }
    }
}
