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

use Ivory\HttpAdapter\Message\Request;
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RequestTest extends AbstractTestCase
{
    /**
     * @var Request
     */
    private $request;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->request = new Request();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Psr\Http\Message\RequestInterface', $this->request);
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\RequestInterface', $this->request);
        $this->assertTrue(in_array(
            'Ivory\HttpAdapter\Message\MessageTrait',
            class_uses('Ivory\HttpAdapter\Message\Request')
        ));
    }

    public function testDefaultState()
    {
        $this->assertEmpty((string) $this->request->getUri());
        $this->assertEmpty($this->request->getMethod());
        $this->assertEmpty($this->request->getHeaders());
        $this->assertEmpty((string) $this->request->getBody());
        $this->assertEmpty($this->request->getParameters());
    }

    public function testInitialState()
    {
        $this->request = new Request(
            $uri = 'http://egeloen.fr/',
            $method = Request::METHOD_POST,
            $body = $this->createMock('Psr\Http\Message\StreamInterface'),
            $headers = ['foo' => ['bar']],
            $parameters = ['baz' => 'bat']
        );

        $headers['Host'] = ['egeloen.fr'];

        $this->assertSame($uri, (string) $this->request->getUri());
        $this->assertSame($method, $this->request->getMethod());
        $this->assertSame($headers, $this->request->getHeaders());
        $this->assertSame($body, $this->request->getBody());
        $this->assertSame($parameters, $this->request->getParameters());
    }
}
