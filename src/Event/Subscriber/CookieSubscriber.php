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
use Ivory\HttpAdapter\Event\ExceptionEvent;
use Ivory\HttpAdapter\Event\MultiExceptionEvent;
use Ivory\HttpAdapter\Event\MultiPostSendEvent;
use Ivory\HttpAdapter\Event\MultiPreSendEvent;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\PreSendEvent;
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
        $this->setCookieJar($cookieJar ?: new CookieJar());
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
     * Sets the cookie jar.
     *
     * @param \Ivory\HttpAdapter\Event\Cookie\Jar\CookieJarInterface $cookieJar The cookie jar.
     */
    public function setCookieJar(CookieJarInterface $cookieJar)
    {
        $this->cookieJar = $cookieJar;
    }

    /**
     * On pre send event.
     *
     * @param \Ivory\HttpAdapter\Event\PreSendEvent $event The pre send event.
     */
    public function onPreSend(PreSendEvent $event)
    {
        $this->cookieJar->populate($event->getRequest());
    }

    /**
     * On post send event.
     *
     * @param \Ivory\HttpAdapter\Event\PostSendEvent $event The post send event.
     */
    public function onPostSend(PostSendEvent $event)
    {
        $this->cookieJar->extract($event->getRequest(), $event->getResponse());
    }

    /**
     * On exception event.
     *
     * @param \Ivory\HttpAdapter\Event\ExceptionEvent $event The exception event.
     */
    public function onException(ExceptionEvent $event)
    {
        if ($event->getException()->hasResponse()) {
            $this->cookieJar->extract($event->getException()->getRequest(), $event->getException()->getResponse());
        }
    }

    /**
     * On multi pre send event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiPreSendEvent $event The multi pre send event.
     */
    public function onMultiPreSend(MultiPreSendEvent $event)
    {
        foreach ($event->getRequests() as $request) {
            $this->cookieJar->populate($request);
        }
    }

    /**
     * On multi post send event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiPostSendEvent $event The multi post send event.
     */
    public function onMultiPostSend(MultiPostSendEvent $event)
    {
        foreach ($event->getResponses() as $response) {
            $this->cookieJar->extract($response->getParameter('request'), $response);
        }
    }

    /**
     * On multi exception event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiExceptionEvent $event The multi exception event.
     */
    public function onMultiException(MultiExceptionEvent $event)
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
            Events::PRE_SEND        => array('onPreSend', 300),
            Events::POST_SEND       => array('onPostSend', 300),
            Events::EXCEPTION       => array('onException', 300),
            Events::MULTI_PRE_SEND  => array('onMultiPreSend', 300),
            Events::MULTI_POST_SEND => array('onMultiPostSend', 300),
            Events::MULTI_EXCEPTION => array('onMultiException', 300),
        );
    }
}
