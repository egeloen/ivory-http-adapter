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

use Ivory\HttpAdapter\Event\ExceptionEvent;
use Ivory\HttpAdapter\Event\Formatter\Formatter;
use Ivory\HttpAdapter\Event\Formatter\FormatterInterface;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\Timer\TimerInterface;

/**
 * Abstract formatter subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractFormatterSubscriber extends AbstractTimerSubscriber
{
    /** @var \Ivory\HttpAdapter\Event\Formatter\FormatterInterface */
    private $formatter;

    /**
     * Creates a formatter subscriber.
     *
     * @param \Ivory\HttpAdapter\Event\Formatter\FormatterInterface|null $formatter The formatter.
     * @param \Ivory\HttpAdapter\Event\Timer\TimerInterface|null         $timer     The timer.
     */
    public function __construct(FormatterInterface $formatter = null, TimerInterface $timer = null)
    {
        parent::__construct($timer);

        $this->setFormatter($formatter ?: new Formatter());
    }

    /**
     * Gets the formatter.
     *
     * @return \Ivory\HttpAdapter\Event\Formatter\FormatterInterface The formatter.
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * Sets the formatter.
     *
     * @param \Ivory\HttpAdapter\Event\Formatter\FormatterInterface $formatter The formatter.
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Formats a post send event.
     *
     * @param \Ivory\HttpAdapter\Event\PostSendEvent $event The post send event.
     *
     * @return array The formatted post send event.
     */
    protected function formatPostSendEvent(PostSendEvent $event)
    {
        return array(
            'adapter'  => $this->getFormatter()->formatHttpAdapter($event->getHttpAdapter()),
            'request'  => $this->getFormatter()->formatRequest($event->getRequest()),
            'response' => $this->getFormatter()->formatResponse($event->getResponse()),
        );
    }

    /**
     * Formats an exception event.
     *
     * @param \Ivory\HttpAdapter\Event\ExceptionEvent $event The exception event.
     *
     * @return array The formatted exception event.
     */
    protected function formatExceptionEvent(ExceptionEvent $event)
    {
        $request = $event->getException()->hasRequest()
            ? $this->formatter->formatRequest($event->getException()->getRequest())
            : null;

        $response = $event->getException()->hasResponse()
            ? $this->formatter->formatResponse($event->getException()->getResponse())
            : null;

        return array(
            'adapter'   => $this->getFormatter()->formatHttpAdapter($event->getHttpAdapter()),
            'exception' => $this->getFormatter()->formatException($event->getException()),
            'request'   => $request,
            'response'  => $response,
        );
    }
}
