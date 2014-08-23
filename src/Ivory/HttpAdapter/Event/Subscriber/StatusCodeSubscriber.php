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
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\HttpAdapterException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Status code subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatusCodeSubscriber implements EventSubscriberInterface
{
    /**
     * On post send event.
     *
     * @param \Ivory\HttpAdapter\Event\PostSendEvent $event The event.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the response status code is an error one.
     */
    public function onPostSend(PostSendEvent $event)
    {
        $statusCode = (string) $event->getResponse()->getStatusCode();

        if ($statusCode[0] === '4' || $statusCode[0] === '5') {
            throw HttpAdapterException::cannotFetchUrl(
                (string) $event->getRequest()->getUrl(),
                $event->getHttpAdapter()->getName(),
                sprintf('Status code: %d', $statusCode)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(Events::POST_SEND => array('onPostSend', 200));
    }
}
