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
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatusCodeSubscriber implements EventSubscriberInterface
{
    /**
     * @var StatusCodeInterface
     */
    private $statusCode;

    /**
     * @param StatusCodeInterface|null $statusCode
     */
    public function __construct(StatusCodeInterface $statusCode = null)
    {
        $this->statusCode = $statusCode ?: new StatusCode();
    }

    /**
     * @return StatusCodeInterface
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param RequestSentEvent $event
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
     * @param MultiRequestSentEvent $event
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
        return [
            Events::REQUEST_SENT       => ['onRequestSent', 200],
            Events::MULTI_REQUEST_SENT => ['onMultiRequestSent', 200],
        ];
    }

    /**
     * @param ResponseInterface        $response
     * @param InternalRequestInterface $internalRequest
     * @param HttpAdapterInterface     $httpAdapter
     *
     * @return HttpAdapterException
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
