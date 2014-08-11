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

use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequest;
use Ivory\HttpAdapter\Message\Response;
use Ivory\HttpAdapter\Message\Stream\StringStream;

/**
 * Abstract subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequest The request.
     */
    protected function createRequest()
    {
        $request = new InternalRequest('http://egeloen.fr', InternalRequest::METHOD_GET);
        $request->setProtocolVersion(InternalRequest::PROTOCOL_VERSION_10);
        $request->setHeaders(array('connection' => 'close'));
        $request->setData(array('foo' => 'bar'));
        $request->setFiles(array('file' => __FILE__));

        return $request;
    }

    /**
     * Creates a response.
     *
     * @return \Ivory\HttpAdapter\Message\Response The response.
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
