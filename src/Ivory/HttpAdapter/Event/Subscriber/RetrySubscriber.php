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
use Ivory\HttpAdapter\Event\Retry\ExponentialDelayedRetryStrategy;
use Ivory\HttpAdapter\Event\Retry\LimitedRetryStrategy;
use Ivory\HttpAdapter\Event\Retry\RetryStrategyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Retry subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RetrySubscriber implements EventSubscriberInterface
{
    /** @const string */
    const RETRY_COUNT = 'retry_count';

    /** @var \Ivory\HttpAdapter\Event\Retry\RetryStrategyInterface */
    protected $strategy;

    /**
     * Creates a retry subscriber.
     *
     * @param \Ivory\HttpAdapter\Event\Retry\RetryStrategyInterface|null $strategy The strategy.
     */
    public function __construct(RetryStrategyInterface $strategy = null)
    {
        $this->setStrategy($strategy ?: new LimitedRetryStrategy(3, new ExponentialDelayedRetryStrategy()));
    }

    /**
     * Gets the strategy.
     *
     * @return \Ivory\HttpAdapter\Event\Retry\RetryStrategyInterface The strategy.
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * Sets the strategy.
     *
     * @param \Ivory\HttpAdapter\Event\Retry\RetryStrategyInterface $strategy The strategy.
     */
    public function setStrategy(RetryStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * {@inheritdoc}
     */
    public function onException(ExceptionEvent $event)
    {
        $request = $event->getRequest();
        $exception = $event->getException();

        if (!$this->strategy->verify($request, $exception)) {
            $request->setParameter(self::RETRY_COUNT, (int) $request->getParameter(self::RETRY_COUNT));

            return;
        }

        if (($delay = $this->strategy->delay($request, $exception)) > 0) {
            usleep($delay * 1000000);
        }

        $request->setParameter(self::RETRY_COUNT, $request->getParameter(self::RETRY_COUNT) + 1);
        $response = $event->getHttpAdapter()->sendInternalRequest($request);

        $event->setResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(Events::EXCEPTION => array('onException', 0));
    }
}
