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
use Ivory\HttpAdapter\Event\ExceptionEvent;
use Ivory\HttpAdapter\Event\MultiExceptionEvent;
use Ivory\HttpAdapter\Event\Retry\Retry;
use Ivory\HttpAdapter\Event\Retry\RetryInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\MultiHttpAdapterException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Retry subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RetrySubscriber implements EventSubscriberInterface
{
    /** @var \Ivory\HttpAdapter\Event\Retry\RetryInterface */
    private $retry;

    /**
     * Creates a retry subscriber.
     *
     * @param \Ivory\HttpAdapter\Event\Retry\RetryInterface|null $retry The retry.
     */
    public function __construct(RetryInterface $retry = null)
    {
        $this->setRetry($retry ?: new Retry());
    }

    /**
     * Gets the retry.
     *
     * @return \Ivory\HttpAdapter\Event\Retry\RetryInterface The retry.
     */
    public function getRetry()
    {
        return $this->retry;
    }

    /**
     * Sets the retry.
     *
     * @param \Ivory\HttpAdapter\Event\Retry\RetryInterface $retry The retry.
     */
    public function setRetry(RetryInterface $retry)
    {
        $this->retry = $retry;
    }

    /**
     * {@inheritdoc}
     */
    public function onException(ExceptionEvent $event)
    {
        if (!$this->retry->retry($event->getException()->getRequest())) {
            return;
        }

        try {
            $event->setResponse($event->getHttpAdapter()->sendRequest($event->getException()->getRequest()));
        } catch (HttpAdapterException $e) {
            $event->setException($e);
        }
    }

    /**
     * On multi exception event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiExceptionEvent $event The multi exception event.
     */
    public function onMultiException(MultiExceptionEvent $event)
    {
        $retryRequests = array();

        foreach ($event->getExceptions() as $exception) {
            if ($this->retry->retry($exception->getRequest(), false)) {
                $retryRequests[] = $exception->getRequest();
                $event->removeException($exception);
            }
        }

        if (empty($retryRequests)) {
            return;
        }

        try {
            $event->addResponses($event->getHttpAdapter()->sendRequests($retryRequests));
        } catch (MultiHttpAdapterException $e) {
            $event->addResponses($e->getResponses());
            $event->addExceptions($e->getExceptions());
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::EXCEPTION       => array('onException', 0),
            Events::MULTI_EXCEPTION => array('onMultiException', 0),
        );
    }
}
