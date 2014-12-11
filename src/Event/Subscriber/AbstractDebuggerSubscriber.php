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

use Ivory\HttpAdapter\Event\ExceptionEvent;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * Abstract debugger subscriber.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractDebuggerSubscriber extends AbstractTimerSubscriber
{
    /**
     * On post send event.
     *
     * @param \Ivory\HttpAdapter\Event\PostSendEvent $event The post send event.
     *
     * @return array The formatted event.
     */
    protected function formatPostSendEvent(PostSendEvent $event)
    {
        return array(
            'time'     => $this->stopTimer($event->getRequest()),
            'adapter'  => $event->getHttpAdapter()->getName(),
            'request'  => $this->formatRequest($event->getRequest()),
            'response' => $this->formatResponse($event->getResponse()),
        );
    }

    /**
     * On exception event.
     *
     * @param \Ivory\HttpAdapter\Event\ExceptionEvent $event The exception event.
     *
     * @return array The formatted event.
     */
    protected function formatExceptionEvent(ExceptionEvent $event)
    {
        return array(
            'time'      => $this->stopTimer($event->getRequest()),
            'adapter'   => $event->getHttpAdapter()->getName(),
            'request'   => $this->formatRequest($event->getRequest()),
            'exception' => $this->formatException($event->getException()),
        );
    }

    /**
     * Formats the request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     *
     * @return array The formatted request.
     */
    private function formatRequest(InternalRequestInterface $request)
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
    private function formatResponse(ResponseInterface $response)
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
    private function formatException(HttpAdapterException $exception)
    {
        return array(
            'code'    => $exception->getCode(),
            'message' => $exception->getMessage(),
            'line'    => $exception->getLine(),
            'file'    => $exception->getFile(),
        );
    }
}
