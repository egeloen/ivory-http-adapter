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
use Ivory\HttpAdapter\Event\Formatter\FormatterInterface;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\PreSendEvent;
use Ivory\HttpAdapter\Event\Timer\TimerInterface;
use Psr\Log\LoggerInterface;

/**
 * Logger subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class LoggerSubscriber extends AbstractFormatterSubscriber
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /**
     * Creates a logger subscriber.
     *
     * @param \Psr\Log\LoggerInterface                                   $logger    The logger.
     * @param \Ivory\HttpAdapter\Event\Formatter\FormatterInterface|null $formatter The formatter.
     * @param \Ivory\HttpAdapter\Event\Timer\TimerInterface|null         $timer     The timer.
     */
    public function __construct(
        LoggerInterface $logger,
        FormatterInterface $formatter = null,
        TimerInterface $timer = null
    ) {
        parent::__construct($formatter, $timer);

        $this->setLogger($logger);
    }

    /**
     * Gets the logger.
     *
     * @return \Psr\Log\LoggerInterface The logger.
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Sets the logger.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * On pre send event.
     *
     * @param \Ivory\HttpAdapter\Event\PreSendEvent $event The pre send event.
     */
    public function onPreSend(PreSendEvent $event)
    {
        $this->getTimer()->start($event->getRequest());
    }

    /**
     * On post send event.
     *
     * @param \Ivory\HttpAdapter\Event\PostSendEvent $event The post send event.
     */
    public function onPostSend(PostSendEvent $event)
    {
        $this->getTimer()->stop($event->getRequest());

        $this->logger->debug(
            sprintf(
                'Send "%s %s" in %.2f ms.',
                $event->getRequest()->getMethod(),
                (string) $event->getRequest()->getUrl(),
                $event->getRequest()->getParameter(TimerInterface::TIME)
            ),
            $this->formatPostSendEvent($event)
        );
    }

    /**
     * On exception event.
     *
     * @param \Ivory\HttpAdapter\Event\ExceptionEvent $event The exception event.
     */
    public function onException(ExceptionEvent $event)
    {
        $this->getTimer()->stop($event->getException()->getRequest());

        $this->logger->error(
            sprintf(
                'Unable to send "%s %s".',
                $event->getException()->getRequest()->getMethod(),
                (string) $event->getException()->getRequest()->getUrl()
            ),
            $this->formatExceptionEvent($event)
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::PRE_SEND  => array('onPreSend', 100),
            Events::POST_SEND => array('onPostSend', 100),
            Events::EXCEPTION => array('onException', 100),
        );
    }
}
