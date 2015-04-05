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
use Ivory\HttpAdapter\Event\RequestErroredEvent;
use Ivory\HttpAdapter\Event\MultiRequestErroredEvent;
use Ivory\HttpAdapter\Event\MultiRequestSentEvent;
use Ivory\HttpAdapter\Event\MultiRequestCreatedEvent;
use Ivory\HttpAdapter\Event\RequestCreatedEvent;
use Ivory\HttpAdapter\Event\RequestSentEvent;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Stopwatch subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StopwatchSubscriber implements EventSubscriberInterface
{
    /** @var \Symfony\Component\Stopwatch\Stopwatch */
    private $stopwatch;

    /**
     * Creates a stopwatch event subscriber.
     *
     * @param \Symfony\Component\Stopwatch\Stopwatch $stopwatch The stopwatch.
     */
    public function __construct(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    /**
     * Gets the stopwatch.
     *
     * @return \Symfony\Component\Stopwatch\Stopwatch The stopwatch.
     */
    public function getStopwatch()
    {
        return $this->stopwatch;
    }

    /**
     * On request created event.
     *
     * @param \Ivory\HttpAdapter\Event\RequestCreatedEvent $event The event.
     */
    public function onRequestCreated(RequestCreatedEvent $event)
    {
        $this->stopwatch->start($this->getStopwatchName($event->getHttpAdapter(), $event->getRequest()));
    }

    /**
     * On request sent event.
     *
     * @param \Ivory\HttpAdapter\Event\RequestSentEvent $event The event.
     */
    public function onRequestSent(RequestSentEvent $event)
    {
        $this->stopwatch->stop($this->getStopwatchName($event->getHttpAdapter(), $event->getRequest()));
    }

    /**
     * On request errored event.
     *
     * @param \Ivory\HttpAdapter\Event\RequestErroredEvent $event The event.
     */
    public function onRequestErrored(RequestErroredEvent $event)
    {
        $this->stopwatch->stop($this->getStopwatchName($event->getHttpAdapter(), $event->getException()->getRequest()));
    }

    /**
     * On multi request created event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiRequestCreatedEvent $event The multi request created event.
     */
    public function onMultiRequestCreated(MultiRequestCreatedEvent $event)
    {
        foreach ($event->getRequests() as $request) {
            $this->stopwatch->start($this->getStopwatchName($event->getHttpAdapter(), $request));
        }
    }

    /**
     * On multi request sent event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiRequestSentEvent $event The multi request sent event.
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
     * On multi request errored event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiRequestErroredEvent $event The multi request errored event.
     */
    public function onMultiResponseErrored(MultiRequestErroredEvent $event)
    {
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
        return array(
            Events::REQUEST_CREATED       => array('onRequestCreated', 10000),
            Events::REQUEST_SENT          => array('onRequestSent', 10000),
            Events::REQUEST_ERRORED       => array('onRequestErrored', 10000),
            Events::MULTI_REQUEST_CREATED => array('onMultiRequestCreated', 10000),
            Events::MULTI_REQUEST_SENT    => array('onMultiRequestSent', 10000),
            Events::MULTI_REQUEST_ERRORED => array('onMultiResponseErrored', 10000),
        );
    }

    /**
     * Gets the stopwatch name.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface             $httpAdapter     The http adapter.
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @return string The stopwatch name.
     */
    private function getStopwatchName(HttpAdapterInterface $httpAdapter, InternalRequestInterface $internalRequest)
    {
        return sprintf('ivory.http_adapter.%s (%s)', $httpAdapter->getName(), (string) $internalRequest->getUri());
    }
}
