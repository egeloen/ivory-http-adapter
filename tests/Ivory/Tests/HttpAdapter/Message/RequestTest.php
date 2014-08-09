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

/**
 * Request test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Message\Request */
    protected $request;

    /** @var string */
    protected $url;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->request = new Request($this->url = 'http://egeloen.fr/');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->url);
        unset($this->request);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Psr\Http\Message\RequestInterface', $this->request);
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\RequestInterface', $this->request);
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\AbstractMessage', $this->request);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->url, $this->request->getUrl());
        $this->assertSame(Request::METHOD_GET, $this->request->getMethod());
        $this->assertFalse($this->request->hasHeaders());
        $this->assertFalse($this->request->hasBody());
        $this->assertSame(Request::PROTOCOL_VERSION_11, $this->request->getProtocolVersion());
    }

    public function testInitialState()
    {
        $this->request = new Request($this->url, $method = Request::METHOD_POST);

        $this->assertSame($method, $this->request->getMethod());
        $this->assertFalse($this->request->hasHeaders());
        $this->assertFalse($this->request->hasBody());
        $this->assertSame(Request::PROTOCOL_VERSION_11, $this->request->getProtocolVersion());
    }

    public function testSetUrl()
    {
        $this->request->setUrl($url = 'http://www.google.com/');

        $this->assertSame($url, $this->request->getUrl());
    }

    public function testSetUrlWithoutScheme()
    {
        $this->request->setUrl($url = 'www.google.com');

        $this->assertSame('http://'.$url, $this->request->getUrl());
    }

    public function testSetMethod()
    {
        $this->request->setMethod($method = Request::METHOD_POST);

        $this->assertSame($method, $this->request->getMethod());
    }

    public function testSetMethodLowercase()
    {
        $this->request->setMethod('post');

        $this->assertSame(Request::METHOD_POST, $this->request->getMethod());
    }
}
