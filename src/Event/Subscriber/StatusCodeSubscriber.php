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
use Ivory\HttpAdapter\Event\StatusCode\StatusCode;
use Ivory\HttpAdapter\Event\StatusCode\StatusCodeInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Status code subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatusCodeSubscriber implements EventSubscriberInterface
{
    /** @var \Ivory\HttpAdapter\Event\StatusCode\StatusCodeInterface */
    private $statusCode;

    /**
     * Creates a status code subscriber.
     *
     * @param \Ivory\HttpAdapter\Event\StatusCode\StatusCodeInterface|null $statusCode The status code.
     */
    public function __construct(StatusCodeInterface $statusCode = null)
    {
        $this->setStatusCode($statusCode ?: new StatusCode());
    }

    /**
     * Gets the status code.
     *
     * @return \Ivory\HttpAdapter\Event\StatusCode\StatusCodeInterface The status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Sets the status code.
     *
     * @param \Ivory\HttpAdapter\Event\StatusCode\StatusCodeInterface $statusCode The status code.
     */
    public function setStatusCode(StatusCodeInterface $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * On post send event.
     *
     * @param \Ivory\HttpAdapter\Event\PostSendEvent $event The event.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the response status code is an error one.
     */
    public function onPostSend(PostSendEvent $event)
    {
        if (!$this->statusCode->validate($event->getResponse())) {
            throw HttpAdapterException::cannotFetchUrl(
                (string) $event->getRequest()->getUrl(),
                $event->getHttpAdapter()->getName(),
                sprintf('Status code: %d', $event->getResponse()->getStatusCode())
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
