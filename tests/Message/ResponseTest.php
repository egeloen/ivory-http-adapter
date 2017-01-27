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
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResponseTest extends AbstractTestCase
{
    /**
     * @var Response
     */
    private $response;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->response = new Response();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $this->response);
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\ResponseInterface', $this->response);
        $this->assertTrue(in_array(
            'Ivory\HttpAdapter\Message\MessageTrait',
            class_uses('Ivory\HttpAdapter\Message\Response')
        ));
    }

    public function testDefaultState()
    {
        $this->assertSame(200, $this->response->getStatusCode());
        $this->assertEmpty($this->response->getHeaders());
        $this->assertEmpty((string) $this->response->getBody());
        $this->assertEmpty($this->response->getParameters());
    }

    public function testInitialState()
    {
        $this->response = new Response(
            $body = $this->createMock('Psr\Http\Message\StreamInterface'),
            $statusCode = 302,
            $headers = ['foo' => ['bar']],
            $parameters = ['baz' => 'bat']
        );

        $this->assertSame($statusCode, $this->response->getStatusCode());
        $this->assertSame($headers, $this->response->getHeaders());
        $this->assertSame($body, $this->response->getBody());
        $this->assertSame($parameters, $this->response->getParameters());
    }
}
