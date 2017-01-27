<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Subscriber;

use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\MultiRequestErroredEvent;
use Ivory\HttpAdapter\Event\RequestErroredEvent;
use Ivory\HttpAdapter\Event\Retry\Retry;
use Ivory\HttpAdapter\Event\Retry\RetryInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\MultiHttpAdapterException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RetrySubscriber implements EventSubscriberInterface
{
    /**
     * @var RetryInterface
     */
    private $retry;

    /**
     * @param RetryInterface|null $retry
     */
    public function __construct(RetryInterface $retry = null)
    {
        $this->retry = $retry ?: new Retry();
    }

    /**
     * @return RetryInterface
     */
    public function getRetry()
    {
        return $this->retry;
    }

    /**
     * @param RequestErroredEvent $event
     */
    public function onRequestErrored(RequestErroredEvent $event)
    {
        if (($request = $this->retry->retry($event->getException()->getRequest())) === false) {
            return;
        }

        $event->getException()->setRequest($request);

        try {
            $event->setResponse($event->getHttpAdapter()->sendRequest($request));
        } catch (HttpAdapterException $e) {
            $event->setException($e);
        }
    }

    /**
     * @param MultiRequestErroredEvent $event
     */
    public function onMultiResponseErrored(MultiRequestErroredEvent $event)
    {
        $retryRequests = [];

        foreach ($event->getExceptions() as $exception) {
            if (($request = $this->retry->retry($exception->getRequest(), false)) !== false) {
                $retryRequests[] = $request;
                $event->removeException($exception);
            }
        }

        if (empty($retryRequests)) {
            return;
        }

        try {
            $event->addResponses($event->getHttpAdapter()->sendRequests($retryRequests));
        } catch (MultiHttpAdapterException $e) {
            $event->addResponses($e->getResponses());
            $event->addExceptions($e->getExceptions());
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::REQUEST_ERRORED       => ['onRequestErrored', 0],
            Events::MULTI_REQUEST_ERRORED => ['onMultiResponseErrored', 0],
        ];
    }
}
