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
use Ivory\HttpAdapter\Event\MultiExceptionEvent;
use Ivory\HttpAdapter\Event\MultiPostSendEvent;
use Ivory\HttpAdapter\Event\MultiPreSendEvent;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\PreSendEvent;
use Ivory\HttpAdapter\Event\Timer\TimerInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
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
        $this->debug($event->getHttpAdapter(), $event->getRequest(), $event->getResponse());
    }

    /**
     * On exception event.
     *
     * @param \Ivory\HttpAdapter\Event\ExceptionEvent $event The exception event.
     */
    public function onException(ExceptionEvent $event)
    {
        $this->error($event->getHttpAdapter(), $event->getException());
    }

    /**
     * On multi pre send event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiPreSendEvent $event The multi pre send event.
     */
    public function onMultiPreSend(MultiPreSendEvent $event)
    {
        foreach ($event->getRequests() as $request) {
            $this->getTimer()->start($request);
        }
    }

    /**
     * On multi post send event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiPostSendEvent $event The multi post send event.
     */
    public function onMultiPostSend(MultiPostSendEvent $event)
    {
        foreach ($event->getResponses() as $response) {
            $this->debug($event->getHttpAdapter(), $response->getParameter('request'), $response);
        }
    }

    /**
     * On multi exception event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiExceptionEvent $event The multi exception event.
     */
    public function onMultiException(MultiExceptionEvent $event)
    {
        foreach ($event->getExceptions() as $exception) {
            $this->error($event->getHttpAdapter(), $exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::PRE_SEND        => array('onPreSend', 100),
            Events::POST_SEND       => array('onPostSend', 100),
            Events::EXCEPTION       => array('onException', 100),
            Events::MULTI_PRE_SEND  => array('onMultiPreSend', 100),
            Events::MULTI_POST_SEND => array('onMultiPostSend', 100),
            Events::MULTI_EXCEPTION => array('onMultiException', 100),
        );
    }

    /**
     * Logs debug.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface             $httpAdapter The http adapter.
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request     The request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response    The response.
     */
    private function debug(
        HttpAdapterInterface $httpAdapter,
        InternalRequestInterface $request,
        ResponseInterface $response
    ) {
        $this->getTimer()->stop($request);

        $this->logger->debug(
            sprintf(
                'Send "%s %s" in %.2f ms.',
                $request->getMethod(),
                (string) $request->getUrl(),
                $request->getParameter(TimerInterface::TIME)
            ),
            array(
                'adapter'  => $httpAdapter->getName(),
                'request'  => $this->getFormatter()->formatRequest($request),
                'response' => $this->getFormatter()->formatResponse($response),
            )
        );
    }

    /**
     * Logs error.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface $httpAdapter The http adapter.
     * @param \Ivory\HttpAdapter\HttpAdapterException $exception   The exception.
     */
    private function error(HttpAdapterInterface $httpAdapter, HttpAdapterException $exception)
    {
        $this->getTimer()->stop($exception->getRequest());

        $this->logger->error(
            sprintf(
                'Unable to send "%s %s".',
                $exception->getRequest()->getMethod(),
                (string) $exception->getRequest()->getUrl()
            ),
            array(
                'adapter'   => $httpAdapter->getName(),
                'exception' => $this->getFormatter()->formatException($exception),
                'request'   => $this->getFormatter()->formatRequest($exception->getRequest()),
                'response'  => $exception->hasResponse()
                    ? $this->getFormatter()->formatResponse($exception->getResponse())
                    : null,
            )
        );
    }
}
