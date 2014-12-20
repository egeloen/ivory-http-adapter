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
use Ivory\HttpAdapter\Event\MultiPostSendEvent;
use Ivory\HttpAdapter\Event\PostSendEvent;
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
        $this->setStatusCode($statusCode ?: new StatusCode());
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
     * Sets the status code.
     *
     * @param \Ivory\HttpAdapter\Event\StatusCode\StatusCodeInterface $statusCode The status code.
     */
    public function setStatusCode(StatusCodeInterface $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * On post send event.
     *
     * @param \Ivory\HttpAdapter\Event\PostSendEvent $event The event.
     */
    public function onPostSend(PostSendEvent $event)
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
     * On multi post send event.
     *
     * @param \Ivory\HttpAdapter\Event\MultiPostSendEvent $event The multi post send event.
     */
    public function onMultiPostSend(MultiPostSendEvent $event)
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
            Events::POST_SEND       => array('onPostSend', 200),
            Events::MULTI_POST_SEND => array('onMultiPostSend', 200),
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
        $exception = HttpAdapterException::cannotFetchUrl(
            (string) $internalRequest->getUrl(),
            $httpAdapter->getName(),
            sprintf('Status code: %d', $response->getStatusCode())
        );

        $exception->setRequest($internalRequest);
        $exception->setResponse($response);

        return $exception;
    }
}
