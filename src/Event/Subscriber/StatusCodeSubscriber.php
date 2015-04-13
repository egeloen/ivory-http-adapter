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
use Ivory\HttpAdapter\Event\MultiRequestSentEvent;
use Ivory\HttpAdapter\Event\RequestSentEvent;
use Ivory\HttpAdapter\Event\StatusCode\StatusCode;
use Ivory\HttpAdapter\Event\StatusCode\StatusCodeInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Status code subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatusCodeSubscriber implements EventSubscriberInterface
{
    /** @var \Ivory\HttpAdapter\Event\StatusCode\StatusCodeInterface */
    private $statusCode;

    /**
     * Creates a status code subscriber.
     *
     * @param \Ivory\HttpAdapter\Event\StatusCode\StatusCodeInterface|null $statusCode The status code.
     */
    public function __construct(StatusCodeInterface $statusCode = null)
    {
        $this->statusCode = $statusCode ?: new StatusCode();
    }

    /**
     * Gets the status code.
     *
     * @return \Ivory\HttpAdapter\Event\StatusCode\StatusCodeInterface The status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * On request sent event.
     *
     * @param \Ivory\HttpAdapter\Event\RequestSentEvent $event The request sent event.
     */
    public function onRequestSent(RequestSentEvent $event)
    {
        if (!$this->statusCode->validate($event->getResponse())) {
            $event->setException($this->createStatusCodeException(
                $event->getResponse(),
                $event->getRequest(),
                $event->getHttpAdapter()
            ));
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
            if (!$this->statusCode->validate($response)) {
                $event->addException($this->createStatusCodeException(
                    $response,
                    $response->getParameter('request'),
                    $event->getHttpAdapter()
                ));

                $event->removeResponse($response);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::REQUEST_SENT       => array('onRequestSent', 200),
            Events::MULTI_REQUEST_SENT => array('onMultiRequestSent', 200),
        );
    }

    /**
     * Creates a status code exception.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response        The response.
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     * @param \Ivory\HttpAdapter\HttpAdapterInterface             $httpAdapter     The http adapter.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The status code exception.
     */
    private function createStatusCodeException(
        ResponseInterface $response,
        InternalRequestInterface $internalRequest,
        HttpAdapterInterface $httpAdapter
    ) {
        $exception = HttpAdapterException::cannotFetchUri(
            (string) $internalRequest->getUri(),
            $httpAdapter->getName(),
            sprintf('Status code: %d', $response->getStatusCode())
        );

        $exception->setRequest($internalRequest);
        $exception->setResponse($response);

        return $exception;
    }
}
