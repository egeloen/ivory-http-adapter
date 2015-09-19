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

use Ivory\HttpAdapter\Event\Cache\CacheInterface;
use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\MultiRequestCreatedEvent;
use Ivory\HttpAdapter\Event\MultiRequestErroredEvent;
use Ivory\HttpAdapter\Event\MultiRequestSentEvent;
use Ivory\HttpAdapter\Event\RequestCreatedEvent;
use Ivory\HttpAdapter\Event\RequestErroredEvent;
use Ivory\HttpAdapter\Event\RequestSentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Cache subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CacheSubscriber implements EventSubscriberInterface
{
    /** @var \Ivory\HttpAdapter\Event\Cache\CacheInterface */
    private $cache;

    /**
     * Creates a cache subscriber.
     *
     * @param \Ivory\HttpAdapter\Event\Cache\CacheInterface $cache The cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->setCache($cache);
    }

    /**
     * Gets the cache.
     *
     * @return \Ivory\HttpAdapter\Event\Cache\CacheInterface The cache.
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Sets the cache.
     *
     * @param \Ivory\HttpAdapter\Event\Cache\CacheInterface $cache The cache.
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * On request created.
     *
     * @param \Ivory\HttpAdapter\Event\RequestCreatedEvent $event
     */
    public function onRequestCreated(RequestCreatedEvent $event)
    {
        $request = $event->getRequest();
        $messageFactory = $event->getHttpAdapter()->getConfiguration()->getMessageFactory();

        if (($response = $this->cache->getResponse($request, $messageFactory)) !== null) {
            $event->setResponse($response);
        } elseif (($exception = $this->cache->getException($request, $messageFactory)) !== null) {
            $event->setException($exception);
        }
    }

    /**
     * On request sent.
     *
     * @param \Ivory\HttpAdapter\Event\RequestSentEvent $event
     */
    public function onRequestSent(RequestSentEvent $event)
    {
        $this->cache->saveResponse($event->getResponse(), $event->getRequest());
    }

    /**
     * On request errored.
     *
     * @param \Ivory\HttpAdapter\Event\RequestErroredEvent $event
     */
    public function onRequestErrored(RequestErroredEvent $event)
    {
        $this->cache->saveException($event->getException(), $event->getException()->getRequest());
    }

    /**
     * On multi request created.
     *
     * @param \Ivory\HttpAdapter\Event\MultiRequestCreatedEvent $event
     */
    public function onMultiRequestCreated(MultiRequestCreatedEvent $event)
    {
        $messageFactory = $event->getHttpAdapter()->getConfiguration()->getMessageFactory();

        foreach ($event->getRequests() as $request) {
            if (($response = $this->cache->getResponse($request, $messageFactory)) !== null) {
                $event->addResponse($response);
                $event->removeRequest($request);
            } elseif (($exception = $this->cache->getException($request, $messageFactory)) !== null) {
                $event->addException($exception);
                $event->removeRequest($request);
            }
        }
    }

    /**
     * On multi request sent.
     *
     * @param \Ivory\HttpAdapter\Event\MultiRequestSentEvent $event
     */
    public function onMultiRequestSent(MultiRequestSentEvent $event)
    {
        foreach ($event->getResponses() as $response) {
            $this->cache->saveResponse($response, $response->getParameter('request'));
        }
    }

    /**
     * On multi request errored.
     *
     * @param \Ivory\HttpAdapter\Event\MultiRequestErroredEvent $event
     */
    public function onMultiRequestErrored(MultiRequestErroredEvent $event)
    {
        foreach ($event->getResponses() as $response) {
            $this->cache->saveResponse($response, $response->getParameter('request'));
        }

        foreach ($event->getExceptions() as $exception) {
            $this->cache->saveException($exception, $exception->getRequest());
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::REQUEST_CREATED       => ['onRequestCreated', -100],
            Events::REQUEST_SENT          => ['onRequestSent', -100],
            Events::REQUEST_ERRORED       => ['onRequestErrored', -100],
            Events::MULTI_REQUEST_CREATED => ['onMultiRequestCreated', -100],
            Events::MULTI_REQUEST_SENT    => ['onMultiRequestSent', -100],
            Events::MULTI_REQUEST_ERRORED => ['onMultiRequestErrored', -100],
        ];
    }
}
