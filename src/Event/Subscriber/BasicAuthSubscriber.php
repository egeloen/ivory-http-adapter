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
 * @author GeLo <geloen.eric@gmail.com>
 */
class BasicAuthSubscriber implements EventSubscriberInterface
{
    /**
     * @var BasicAuthInterface
     */
    private $basicAuth;

    /**
     * @param BasicAuthInterface $basicAuth
     */
    public function __construct(BasicAuthInterface $basicAuth)
    {
        $this->basicAuth = $basicAuth;
    }

    /**
     * @return BasicAuthInterface
     */
    public function getBasicAuth()
    {
        return $this->basicAuth;
    }

    /**
     * @param RequestCreatedEvent $event
     */
    public function onRequestCreated(RequestCreatedEvent $event)
    {
        $event->setRequest($this->basicAuth->authenticate($event->getRequest()));
    }

    /**
     * @param MultiRequestCreatedEvent $event
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
        return [
            Events::REQUEST_CREATED       => ['onRequestCreated', 300],
            Events::MULTI_REQUEST_CREATED => ['onMultiRequestCreated', 300],
        ];
    }
}
