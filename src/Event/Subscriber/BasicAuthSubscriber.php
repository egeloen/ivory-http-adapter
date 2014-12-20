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

use Ivory\HttpAdapter\Event\BasicAuth\BasicAuthInterface;
use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\MultiPreSendEvent;
use Ivory\HttpAdapter\Event\PreSendEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Basic auth subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class BasicAuthSubscriber implements EventSubscriberInterface
{
    /** @var \Ivory\HttpAdapter\Event\BasicAuth\BasicAuthInterface */
    private $basicAuth;

    /**
     * Creates a basic auth subscriber.
     *
     * @param \Ivory\HttpAdapter\Event\BasicAuth\BasicAuthInterface $basicAuth The basic auth.
     */
    public function __construct(BasicAuthInterface $basicAuth)
    {
        $this->setBasicAuth($basicAuth);
    }

    /**
     * Gets the basic auth.
     *
     * @return \Ivory\HttpAdapter\Event\BasicAuth\BasicAuthInterface The basic auth.
     */
    public function getBasicAuth()
    {
        return $this->basicAuth;
    }

    /**
     * Sets the basic auth.
     *
     * @param \Ivory\HttpAdapter\Event\BasicAuth\BasicAuthInterface $basicAuth The basic auth.
     */
    public function setBasicAuth(BasicAuthInterface $basicAuth)
    {
        $this->basicAuth = $basicAuth;
    }

    /**
     * On pre send event.
     *
     * @param \Ivory\HttpAdapter\Event\PreSendEvent $event The pre send event.
     */
    public function onPreSend(PreSendEvent $event)
    {
        $this->basicAuth->authenticate($event->getRequest());
    }

    /**
     * On multi pre send event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiPreSendEvent $event The multi pre send event.
     */
    public function onMultiPreSend(MultiPreSendEvent $event)
    {
        foreach ($event->getRequests() as $request) {
            $this->basicAuth->authenticate($request);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::PRE_SEND       => array('onPreSend', 300),
            Events::MULTI_PRE_SEND => array('onMultiPreSend', 300),
        );
    }
}
