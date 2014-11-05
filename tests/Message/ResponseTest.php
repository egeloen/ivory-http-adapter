<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Message;

use Ivory\HttpAdapter\Message\Response;

/**
 * Response test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Message\Response */
    protected $response;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->response = new Response();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->response);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Psr\Http\Message\IncomingResponseInterface', $this->response);
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\ResponseInterface', $this->response);
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\AbstractMessage', $this->response);
    }

    public function testDefaultState()
    {
        $this->assertSame(Response::PROTOCOL_VERSION_1_1, $this->response->getProtocolVersion());
        $this->assertSame(200, $this->response->getStatusCode());
        $this->assertSame('OK', $this->response->getReasonPhrase());

        $this->assertFalse($this->response->hasHeaders());
        $this->assertEmpty($this->response->getHeaders());

        $this->assertFalse($this->response->hasBody());
        $this->assertNull($this->response->getBody());

        $this->assertFalse($this->response->hasParameters());
        $this->assertEmpty($this->response->getParameters());
    }

    public function testInitialState()
    {
        $this->response = new Response(
            $statusCode = 404,
            $reasonPhrase = 'Not Found',
            $protocolVersion = Response::PROTOCOL_VERSION_1_0,
            $headers = array('foo' => array('bar')),
            $body = $this->getMock('Psr\Http\Message\StreamableInterface'),
            $parameters = array('baz' => 'bat')
        );

        $this->assertSame($protocolVersion, $this->response->getProtocolVersion());
        $this->assertSame($statusCode, $this->response->getStatusCode());
        $this->assertSame($reasonPhrase, $this->response->getReasonPhrase());

        $this->assertTrue($this->response->hasHeaders());
        $this->assertSame($headers, $this->response->getHeaders());

        $this->assertTrue($this->response->hasBody());
        $this->assertSame($body, $this->response->getBody());

        $this->assertTrue($this->response->hasParameters());
        $this->assertSame($parameters, $this->response->getParameters());
    }
}
