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
    protected $cookieJar;

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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::PRE_SEND  => array('onPreSend', 300),
            Events::POST_SEND => array('onPostSend', 300),
        );
    }
}
