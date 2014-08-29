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
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Logger subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class LoggerSubscriber extends AbstractTimerSubscriber
{
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

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
     * {@inheritdoc}
     */
    public function onPostSend(PostSendEvent $event)
    {
        parent::onPostSend($event);

        $this->logger->debug(
            sprintf(
                'Send "%s %s" in %.2f ms.',
                $event->getRequest()->getMethod(),
                (string) $event->getRequest()->getUrl(),
                $this->time
            ),
            array(
                'time'     => $this->time,
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
            sprintf(
                'Unable to send "%s %s".',
                $event->getRequest()->getMethod(),
                (string) $event->getRequest()->getUrl()
            ),
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
            Events::PRE_SEND  => array('onPreSend', 100),
            Events::POST_SEND => array('onPostSend', 100),
            Events::EXCEPTION => array('onException', 100),
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
            'url'              => (string) $request->getUrl(),
            'method'           => $request->getMethod(),
            'headers'          => $request->getHeaders(),
            'raw_datas'        => $request->getRawDatas(),
            'datas'            => $request->getDatas(),
            'files'            => $request->getFiles(),
            'parameters'       => $request->getParameters(),
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
            'parameters'       => $response->getParameters(),
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
