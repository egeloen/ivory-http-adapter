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
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $this->response);
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\ResponseInterface', $this->response);
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\AbstractMessage', $this->response);
    }

    public function testDefaultState()
    {
        $this->assertNull($this->response->getProtocolVersion());
        $this->assertNull($this->response->getStatusCode());
        $this->assertNull($this->response->getReasonPhrase());
        $this->assertFalse($this->response->hasHeaders());
        $this->assertFalse($this->response->hasBody());
        $this->assertNull($this->response->getEffectiveUrl());
    }

    public function testSetProtocolVersion()
    {
        $this->response->setProtocolVersion($protocolVersion = Response::PROTOCOL_VERSION_11);

        $this->assertSame($protocolVersion, $this->response->getProtocolVersion());
    }

    public function testSetStatusCode()
    {
        $this->response->setStatusCode($statusCode = 200);

        $this->assertSame($statusCode, $this->response->getStatusCode());
    }

    public function testSetReasonPhrase()
    {
        $this->response->setReasonPhrase($reasonPhrase = 'OK');

        $this->assertSame($reasonPhrase, $this->response->getReasonPhrase());
    }

    public function testSetEffectiveUrl()
    {
        $this->response->setEffectiveUrl($effectiveUrl = 'http://egeloen.fr/');

        $this->assertSame($effectiveUrl, $this->response->getEffectiveUrl());
    }
}
