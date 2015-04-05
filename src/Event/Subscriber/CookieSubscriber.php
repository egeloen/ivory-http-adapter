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

use Ivory\HttpAdapter\Event\Cookie\Jar\CookieJar;
use Ivory\HttpAdapter\Event\Cookie\Jar\CookieJarInterface;
use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\RequestErroredEvent;
use Ivory\HttpAdapter\Event\MultiRequestErroredEvent;
use Ivory\HttpAdapter\Event\MultiRequestSentEvent;
use Ivory\HttpAdapter\Event\MultiRequestCreatedEvent;
use Ivory\HttpAdapter\Event\RequestSentEvent;
use Ivory\HttpAdapter\Event\RequestCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Cookie subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CookieSubscriber implements EventSubscriberInterface
{
    /** @var \Ivory\HttpAdapter\Event\Cookie\Jar\CookieJarInterface */
    private $cookieJar;

    /**
     * Creates a cookie subscriber.
     *
     * @param \Ivory\HttpAdapter\Event\Cookie\Jar\CookieJarInterface|null $cookieJar The cookie jar.
     */
    public function __construct(CookieJarInterface $cookieJar = null)
    {
        $this->cookieJar = $cookieJar ?: new CookieJar();
    }

    /**
     * Gets the cookie jar.
     *
     * @return \Ivory\HttpAdapter\Event\Cookie\Jar\CookieJarInterface The cookie jar.
     */
    public function getCookieJar()
    {
        return $this->cookieJar;
    }

    /**
     * On request created event.
     *
     * @param \Ivory\HttpAdapter\Event\RequestCreatedEvent $event The request created event.
     */
    public function onRequestCreated(RequestCreatedEvent $event)
    {
        $event->setRequest($this->cookieJar->populate($event->getRequest()));
    }

    /**
     * On request sent event.
     *
     * @param \Ivory\HttpAdapter\Event\RequestSentEvent $event The request sent event.
     */
    public function onRequestSent(RequestSentEvent $event)
    {
        $this->cookieJar->extract($event->getRequest(), $event->getResponse());
    }

    /**
     * On request errored event.
     *
     * @param \Ivory\HttpAdapter\Event\RequestErroredEvent $event The request errored event.
     */
    public function onRequestErrored(RequestErroredEvent $event)
    {
        if ($event->getException()->hasResponse()) {
            $this->cookieJar->extract($event->getException()->getRequest(), $event->getException()->getResponse());
        }
    }

    /**
     * On multi request created event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiRequestCreatedEvent $event The multi request created event.
     */
    public function onMultiRequestCreated(MultiRequestCreatedEvent $event)
    {
        foreach ($event->getRequests() as $request) {
            $event->removeRequest($request);
            $event->addRequest($this->cookieJar->populate($request));
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
            $this->cookieJar->extract($response->getParameter('request'), $response);
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
            if ($exception->hasResponse()) {
                $this->cookieJar->extract($exception->getRequest(), $exception->getResponse());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::REQUEST_CREATED       => array('onRequestCreated', 300),
            Events::REQUEST_SENT          => array('onRequestSent', 300),
            Events::REQUEST_ERRORED       => array('onRequestErrored', 300),
            Events::MULTI_REQUEST_CREATED => array('onMultiRequestCreated', 300),
            Events::MULTI_REQUEST_SENT    => array('onMultiRequestSent', 300),
            Events::MULTI_REQUEST_ERRORED => array('onMultiResponseErrored', 300),
        );
    }
}
