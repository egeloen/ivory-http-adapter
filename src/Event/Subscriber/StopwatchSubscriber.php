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
use Ivory\HttpAdapter\Event\MultiRequestCreatedEvent;
use Ivory\HttpAdapter\Event\MultiRequestErroredEvent;
use Ivory\HttpAdapter\Event\MultiRequestSentEvent;
use Ivory\HttpAdapter\Event\RequestCreatedEvent;
use Ivory\HttpAdapter\Event\RequestErroredEvent;
use Ivory\HttpAdapter\Event\RequestSentEvent;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class StopwatchSubscriber implements EventSubscriberInterface
{
    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * @param Stopwatch $stopwatch
     */
    public function __construct(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    /**
     * @return Stopwatch
     */
    public function getStopwatch()
    {
        return $this->stopwatch;
    }

    /**
     * @param RequestCreatedEvent $event
     */
    public function onRequestCreated(RequestCreatedEvent $event)
    {
        $this->stopwatch->start($this->getStopwatchName($event->getHttpAdapter(), $event->getRequest()));
    }

    /**
     * @param RequestSentEvent $event
     */
    public function onRequestSent(RequestSentEvent $event)
    {
        if (!$event->hasException()) {
            $this->stopwatch->stop($this->getStopwatchName($event->getHttpAdapter(), $event->getRequest()));
        }
    }

    /**
     * @param RequestErroredEvent $event
     */
    public function onRequestErrored(RequestErroredEvent $event)
    {
        $this->stopwatch->stop($this->getStopwatchName($event->getHttpAdapter(), $event->getException()->getRequest()));
    }

    /**
     * @param MultiRequestCreatedEvent $event
     */
    public function onMultiRequestCreated(MultiRequestCreatedEvent $event)
    {
        foreach ($event->getRequests() as $request) {
            $this->stopwatch->start($this->getStopwatchName($event->getHttpAdapter(), $request));
        }
    }

    /**
     * @param MultiRequestSentEvent $event
     */
    public function onMultiRequestSent(MultiRequestSentEvent $event)
    {
        foreach ($event->getResponses() as $response) {
            $this->stopwatch->stop(
                $this->getStopwatchName($event->getHttpAdapter(), $response->getParameter('request'))
            );
        }
    }

    /**
     * @param MultiRequestErroredEvent $event
     */
    public function onMultiResponseErrored(MultiRequestErroredEvent $event)
    {
        foreach ($event->getResponses() as $response) {
            $this->stopwatch->stop(
                $this->getStopwatchName($event->getHttpAdapter(), $response->getParameter('request'))
            );
        }

        foreach ($event->getExceptions() as $exception) {
            $this->stopwatch->stop(
                $this->getStopwatchName($event->getHttpAdapter(), $exception->getRequest())
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::REQUEST_CREATED       => ['onRequestCreated', 10000],
            Events::REQUEST_SENT          => ['onRequestSent', -10000],
            Events::REQUEST_ERRORED       => ['onRequestErrored', -10000],
            Events::MULTI_REQUEST_CREATED => ['onMultiRequestCreated', 10000],
            Events::MULTI_REQUEST_SENT    => ['onMultiRequestSent', -10000],
            Events::MULTI_REQUEST_ERRORED => ['onMultiResponseErrored', -10000],
        ];
    }

    /**
     * @param HttpAdapterInterface     $httpAdapter
     * @param InternalRequestInterface $internalRequest
     *
     * @return string
     */
    private function getStopwatchName(HttpAdapterInterface $httpAdapter, InternalRequestInterface $internalRequest)
    {
        return sprintf('ivory.http_adapter.%s (%s)', $httpAdapter->getName(), (string) $internalRequest->getUri());
    }
}
