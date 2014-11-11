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
use Ivory\HttpAdapter\Event\PostSendEvent;
use Psr\Log\LoggerInterface;

/**
 * Logger subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class LoggerSubscriber extends AbstractDebuggerSubscriber
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /**
     * Creates a logger subscriber.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     */
    public function __construct(LoggerInterface $logger)
    {
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
     * On post send event.
     *
     * @param \Ivory\HttpAdapter\Event\PostSendEvent $event The post send event.
     */
    public function onPostSend(PostSendEvent $event)
    {
        $datas = parent::onPostSend($event);

        $this->logger->debug(
            sprintf(
                'Send "%s %s" in %.2f ms.',
                $event->getRequest()->getMethod(),
                (string) $event->getRequest()->getUrl(),
                $datas['time']
            ),
            $datas
        );
    }

    /**
     * On exception event.
     *
     * @param \Ivory\HttpAdapter\Event\ExceptionEvent $event The exception event.
     */
    public function onException(ExceptionEvent $event)
    {
        $this->logger->error(
            sprintf(
                'Unable to send "%s %s".',
                $event->getRequest()->getMethod(),
                (string) $event->getRequest()->getUrl()
            ),
            parent::onException($event)
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
