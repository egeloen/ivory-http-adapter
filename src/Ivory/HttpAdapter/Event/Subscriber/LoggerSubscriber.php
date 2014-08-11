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
use Ivory\HttpAdapter\Event\PreSendEvent;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Logger subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class LoggerSubscriber implements EventSubscriberInterface
{
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var float */
    protected $start;

    /**
     * Creates a logger subscriber.
     *
     * @param \Psr\Log\LoggerInterface $logger The logger.
     */
    public function __construct(LoggerInterface $logger)
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
        $this->start = microtime(true);
    }

    /**
     * On post send event.
     *
     * @param \Ivory\HttpAdapter\Event\PostSendEvent $event The post send event.
     */
    public function onPostSend(PostSendEvent $event)
    {
        $time = microtime(true) - $this->start;

        $this->logger->debug(
            sprintf(
                'Send "%s %s" in %.2f ms.',
                $event->getRequest()->getMethod(),
                $event->getRequest()->getUrl(),
                $time
            ),
            array(
                'time'     => $time,
                'request'  => $this->formatRequest($event->getRequest()),
                'response' => $this->formatResponse($event->getResponse()),
            )
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
            sprintf('Unable to send "%s %s".', $event->getRequest()->getMethod(), $event->getRequest()->getUrl()),
            array(
                'request'   => $this->formatRequest($event->getRequest()),
                'exception' => $this->formatException($event->getException()),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::PRE_SEND  => 'onPreSend',
            Events::POST_SEND => 'onPostSend',
            Events::EXCEPTION => "onException",
        );
    }

    /**
     * Formats the request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     *
     * @return array The formatted request.
     */
    protected function formatRequest(InternalRequestInterface $request)
    {
        return array(
            'protocol_version' => $request->getProtocolVersion(),
            'url'              => $request->getUrl(),
            'method'           => $request->getMethod(),
            'headers'          => $request->getHeaders(),
            'data'             => $request->getData(),
            'files'            => $request->getFiles(),
        );
    }

    /**
     * Formats the response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface $response The response.
     *
     * @return array The formatted response.
     */
    protected function formatResponse(ResponseInterface $response)
    {
        return array(
            'protocol_version' => $response->getProtocolVersion(),
            'status_code'      => $response->getStatusCode(),
            'reason_phrase'    => $response->getReasonPhrase(),
            'headers'          => $response->getHeaders(),
            'body'             => (string) $response->getBody(),
            'effective_url'    => $response->getEffectiveUrl(),
        );
    }

    /**
     * Formats the exception.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterException $exception The exception.
     *
     * @return array The formatted exception.
     */
    protected function formatException(HttpAdapterException $exception)
    {
        return array(
            'code'    => $exception->getCode(),
            'message' => $exception->getMessage(),
            'line'    => $exception->getLine(),
            'file'    => $exception->getFile(),
        );
    }
}
