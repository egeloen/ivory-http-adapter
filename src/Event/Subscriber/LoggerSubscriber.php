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
use Ivory\HttpAdapter\Event\Formatter\FormatterInterface;
use Ivory\HttpAdapter\Event\MultiRequestCreatedEvent;
use Ivory\HttpAdapter\Event\MultiRequestErroredEvent;
use Ivory\HttpAdapter\Event\MultiRequestSentEvent;
use Ivory\HttpAdapter\Event\RequestCreatedEvent;
use Ivory\HttpAdapter\Event\RequestErroredEvent;
use Ivory\HttpAdapter\Event\RequestSentEvent;
use Ivory\HttpAdapter\Event\Timer\TimerInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LoggerSubscriber extends AbstractFormatterSubscriber
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface         $logger
     * @param FormatterInterface|null $formatter
     * @param TimerInterface|null     $timer
     */
    public function __construct(
        LoggerInterface $logger,
        FormatterInterface $formatter = null,
        TimerInterface $timer = null
    ) {
        parent::__construct($formatter, $timer);

        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param RequestCreatedEvent $event
     */
    public function onRequestCreated(RequestCreatedEvent $event)
    {
        $event->setRequest($this->getTimer()->start($event->getRequest()));
    }

    /**
     * @param RequestSentEvent $event
     */
    public function onRequestSent(RequestSentEvent $event)
    {
        $event->setRequest($this->debug($event->getHttpAdapter(), $event->getRequest(), $event->getResponse()));
    }

    /**
     * @param RequestErroredEvent $event
     */
    public function onRequestErrored(RequestErroredEvent $event)
    {
        $event->getException()->setRequest($this->error($event->getHttpAdapter(), $event->getException()));
    }

    /**
     * @param MultiRequestCreatedEvent $event
     */
    public function onMultiRequestCreated(MultiRequestCreatedEvent $event)
    {
        foreach ($event->getRequests() as $request) {
            $event->removeRequest($request);
            $event->addRequest($this->getTimer()->start($request));
        }
    }

    /**
     * @param MultiRequestSentEvent $event
     */
    public function onMultiRequestSent(MultiRequestSentEvent $event)
    {
        foreach ($event->getResponses() as $response) {
            $request = $this->debug($event->getHttpAdapter(), $response->getParameter('request'), $response);

            $event->removeResponse($response);
            $event->addResponse($response->withParameter('request', $request));
        }
    }

    /**
     * @param MultiRequestErroredEvent $event
     */
    public function onMultiResponseErrored(MultiRequestErroredEvent $event)
    {
        foreach ($event->getExceptions() as $exception) {
            $exception->setRequest($this->error($event->getHttpAdapter(), $exception));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::REQUEST_CREATED       => ['onRequestCreated', 100],
            Events::REQUEST_SENT          => ['onRequestSent', 100],
            Events::REQUEST_ERRORED       => ['onRequestErrored', 100],
            Events::MULTI_REQUEST_CREATED => ['onMultiRequestCreated', 100],
            Events::MULTI_REQUEST_SENT    => ['onMultiRequestSent', 100],
            Events::MULTI_REQUEST_ERRORED => ['onMultiResponseErrored', 100],
        ];
    }

    /**
     * @param HttpAdapterInterface     $httpAdapter
     * @param InternalRequestInterface $request
     * @param ResponseInterface        $response
     *
     * @return InternalRequestInterface
     */
    private function debug(
        HttpAdapterInterface $httpAdapter,
        InternalRequestInterface $request,
        ResponseInterface $response
    ) {
        $request = $this->getTimer()->stop($request);

        $this->logger->debug(
            sprintf(
                'Send "%s %s" in %.2f ms.',
                $request->getMethod(),
                (string) $request->getUri(),
                $request->getParameter(TimerInterface::TIME)
            ),
            [
                'adapter'  => $httpAdapter->getName(),
                'request'  => $this->getFormatter()->formatRequest($request),
                'response' => $this->getFormatter()->formatResponse($response),
            ]
        );

        return $request;
    }

    /**
     * @param HttpAdapterInterface $httpAdapter
     * @param HttpAdapterException $exception
     *
     * @return InternalRequestInterface
     */
    private function error(HttpAdapterInterface $httpAdapter, HttpAdapterException $exception)
    {
        $request = $this->getTimer()->stop($exception->getRequest());

        $this->logger->error(
            sprintf(
                'Unable to send "%s %s".',
                $exception->getRequest()->getMethod(),
                (string) $exception->getRequest()->getUri()
            ),
            [
                'adapter'   => $httpAdapter->getName(),
                'exception' => $this->getFormatter()->formatException($exception),
                'request'   => $this->getFormatter()->formatRequest($request),
                'response'  => $exception->hasResponse()
                    ? $this->getFormatter()->formatResponse($exception->getResponse())
                    : null,
            ]
        );

        return $request;
    }
}
