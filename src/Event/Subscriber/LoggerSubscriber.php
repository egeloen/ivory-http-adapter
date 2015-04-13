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
use Ivory\HttpAdapter\Event\RequestErroredEvent;
use Ivory\HttpAdapter\Event\Formatter\FormatterInterface;
use Ivory\HttpAdapter\Event\MultiRequestErroredEvent;
use Ivory\HttpAdapter\Event\MultiRequestSentEvent;
use Ivory\HttpAdapter\Event\MultiRequestCreatedEvent;
use Ivory\HttpAdapter\Event\RequestSentEvent;
use Ivory\HttpAdapter\Event\RequestCreatedEvent;
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

        $this->logger = $logger;
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
     * On request created event.
     *
     * @param \Ivory\HttpAdapter\Event\RequestCreatedEvent $event The request created event.
     */
    public function onRequestCreated(RequestCreatedEvent $event)
    {
        $event->setRequest($this->getTimer()->start($event->getRequest()));
    }

    /**
     * On request sent event.
     *
     * @param \Ivory\HttpAdapter\Event\RequestSentEvent $event The request sent event.
     */
    public function onRequestSent(RequestSentEvent $event)
    {
        $event->setRequest($this->debug($event->getHttpAdapter(), $event->getRequest(), $event->getResponse()));
    }

    /**
     * On request errored event.
     *
     * @param \Ivory\HttpAdapter\Event\RequestErroredEvent $event The request errored event.
     */
    public function onRequestErrored(RequestErroredEvent $event)
    {
        $event->getException()->setRequest($this->error($event->getHttpAdapter(), $event->getException()));
    }

    /**
     * On multi request created event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiRequestCreatedEvent $event The multi request created event.
     */
    public function onMultiRequestCreated(MultiRequestCreatedEvent $event)
    {
        foreach ($event->getRequests() as $request) {
            $event->removeRequest($request);
            $event->addRequest($this->getTimer()->start($request));
        }
    }

    /**
     * On multi request sent event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiRequestSentEvent $event The multi request sent event.
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
     * On multi request errored event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiRequestErroredEvent $event The multi request errored event.
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
        return array(
            Events::REQUEST_CREATED       => array('onRequestCreated', 100),
            Events::REQUEST_SENT          => array('onRequestSent', 100),
            Events::REQUEST_ERRORED       => array('onRequestErrored', 100),
            Events::MULTI_REQUEST_CREATED => array('onMultiRequestCreated', 100),
            Events::MULTI_REQUEST_SENT    => array('onMultiRequestSent', 100),
            Events::MULTI_REQUEST_ERRORED => array('onMultiResponseErrored', 100),
        );
    }

    /**
     * Logs debug.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface             $httpAdapter The http adapter.
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request     The request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response    The response.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The logged request.
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
            array(
                'adapter'  => $httpAdapter->getName(),
                'request'  => $this->getFormatter()->formatRequest($request),
                'response' => $this->getFormatter()->formatResponse($response),
            )
        );

        return $request;
    }

    /**
     * Logs error.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface $httpAdapter The http adapter.
     * @param \Ivory\HttpAdapter\HttpAdapterException $exception   The exception.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The logged request.
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
            array(
                'adapter'   => $httpAdapter->getName(),
                'exception' => $this->getFormatter()->formatException($exception),
                'request'   => $this->getFormatter()->formatRequest($request),
                'response'  => $exception->hasResponse()
                    ? $this->getFormatter()->formatResponse($exception->getResponse())
                    : null,
            )
        );

        return $request;
    }
}
