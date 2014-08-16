<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Subscriber;

use Ivory\HttpAdapter\Event\ExceptionEvent;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\PreSendEvent;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequest;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\Response;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\HttpAdapter\Message\Stream\StringStream;

/**
 * Abstract subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a pre send event.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface|null $request The request.
     *
     * @return \Ivory\HttpAdapter\Event\PreSendEvent The pre send event.
     */
    protected function createPreSendEvent(InternalRequestInterface $request = null)
    {
        return new PreSendEvent($request ?: $this->createRequest());
    }

    /**
     * Creates a post send event.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface|null $request  The request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface|null        $response The response.
     *
     * @return \Ivory\HttpAdapter\Event\PostSendEvent The post send event.
     */
    protected function createPostSendEvent(InternalRequestInterface $request = null, ResponseInterface $response = null)
    {
        return new PostSendEvent($request ?: $this->createRequest(), $response ?: $this->createResponse());
    }

    /**
     * Creates an exception event.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface|null $request   The request.
     * @param \Ivory\HttpAdapter\HttpAdapterException|null             $exception The exception.
     *
     * @return \Ivory\HttpAdapter\Event\ExceptionEvent The exception event.
     */
    protected function createExceptionEvent(
        InternalRequestInterface $request = null,
        HttpAdapterException $exception = null
    ) {
        return new ExceptionEvent($request ?: $this->createRequest(), $exception ?: $this->createException());
    }

    /**
     * Creates a request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The request.
     */
    protected function createRequest()
    {
        $request = new InternalRequest('http://egeloen.fr', InternalRequest::METHOD_GET);
        $request->setProtocolVersion(InternalRequest::PROTOCOL_VERSION_10);
        $request->setHeaders(array('connection' => 'close'));
        $request->setDatas(array('foo' => 'bar'));
        $request->setFiles(array('file' => __FILE__));

        return $request;
    }

    /**
     * Creates a response.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    protected function createResponse()
    {
        $response = new Response();
        $response->setProtocolVersion(Response::PROTOCOL_VERSION_11);
        $response->setStatusCode(200);
        $response->setReasonPhrase('OK');
        $response->setHeaders(array('transfer-encoding' => 'chunked'));
        $response->setBody(new StringStream('foo'));
        $response->setEffectiveUrl('http://www.google.com');

        return $response;
    }

    /**
     * Creates an exception.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException The exception.
     */
    protected function createException()
    {
        return new HttpAdapterException('message', 123);
    }
}
