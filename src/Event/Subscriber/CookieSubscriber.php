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
use Ivory\HttpAdapter\Event\MultiRequestCreatedEvent;
use Ivory\HttpAdapter\Event\MultiRequestErroredEvent;
use Ivory\HttpAdapter\Event\MultiRequestSentEvent;
use Ivory\HttpAdapter\Event\RequestCreatedEvent;
use Ivory\HttpAdapter\Event\RequestErroredEvent;
use Ivory\HttpAdapter\Event\RequestSentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CookieSubscriber implements EventSubscriberInterface
{
    /**
     * @var CookieJarInterface
     */
    private $cookieJar;

    /**
     * @param CookieJarInterface|null $cookieJar
     */
    public function __construct(CookieJarInterface $cookieJar = null)
    {
        $this->cookieJar = $cookieJar ?: new CookieJar();
    }

    /**
     * @return CookieJarInterface
     */
    public function getCookieJar()
    {
        return $this->cookieJar;
    }

    /**
     * @param RequestCreatedEvent $event
     */
    public function onRequestCreated(RequestCreatedEvent $event)
    {
        $event->setRequest($this->cookieJar->populate($event->getRequest()));
    }

    /**
     * @param RequestSentEvent $event
     */
    public function onRequestSent(RequestSentEvent $event)
    {
        $this->cookieJar->extract($event->getRequest(), $event->getResponse());
    }

    /**
     * @param RequestErroredEvent $event
     */
    public function onRequestErrored(RequestErroredEvent $event)
    {
        if ($event->getException()->hasResponse()) {
            $this->cookieJar->extract($event->getException()->getRequest(), $event->getException()->getResponse());
        }
    }

    /**
     * @param MultiRequestCreatedEvent $event
     */
    public function onMultiRequestCreated(MultiRequestCreatedEvent $event)
    {
        foreach ($event->getRequests() as $request) {
            $event->removeRequest($request);
            $event->addRequest($this->cookieJar->populate($request));
        }
    }

    /**
     * @param MultiRequestSentEvent $event
     */
    public function onMultiRequestSent(MultiRequestSentEvent $event)
    {
        foreach ($event->getResponses() as $response) {
            $this->cookieJar->extract($response->getParameter('request'), $response);
        }
    }

    /**
     * @param MultiRequestErroredEvent $event
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
        return [
            Events::REQUEST_CREATED       => ['onRequestCreated', 300],
            Events::REQUEST_SENT          => ['onRequestSent', 300],
            Events::REQUEST_ERRORED       => ['onRequestErrored', 300],
            Events::MULTI_REQUEST_CREATED => ['onMultiRequestCreated', 300],
            Events::MULTI_REQUEST_SENT    => ['onMultiRequestSent', 300],
            Events::MULTI_REQUEST_ERRORED => ['onMultiResponseErrored', 300],
        ];
    }
}
