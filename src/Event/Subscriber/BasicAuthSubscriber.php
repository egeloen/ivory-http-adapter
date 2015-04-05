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
use Ivory\HttpAdapter\Event\MultiRequestCreatedEvent;
use Ivory\HttpAdapter\Event\RequestCreatedEvent;
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
        $this->basicAuth = $basicAuth;
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
     * On request created event.
     *
     * @param \Ivory\HttpAdapter\Event\RequestCreatedEvent $event The request created event.
     */
    public function onRequestCreated(RequestCreatedEvent $event)
    {
        $event->setRequest($this->basicAuth->authenticate($event->getRequest()));
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
            $event->addRequest($this->basicAuth->authenticate($request));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::REQUEST_CREATED       => array('onRequestCreated', 300),
            Events::MULTI_REQUEST_CREATED => array('onMultiRequestCreated', 300),
        );
    }
}
