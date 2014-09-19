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
use Ivory\Tests\HttpAdapter\Normalizer\AbstractUrlNormalizerTest;

/**
 * Request test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RequestTest extends AbstractUrlNormalizerTest
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
        $this->assertSame(Request::PROTOCOL_VERSION_1_1, $this->request->getProtocolVersion());
    }

    public function testInitialState()
    {
        $this->request = new Request($this->url, $method = Request::METHOD_POST);

        $this->assertSame($method, $this->request->getMethod());
        $this->assertFalse($this->request->hasHeaders());
        $this->assertFalse($this->request->hasBody());
        $this->assertSame(Request::PROTOCOL_VERSION_1_1, $this->request->getProtocolVersion());
    }

    /**
     * @dataProvider validUrlProvider
     */
    public function testSetUrlWithValidUrl($url)
    {
        $this->request->setUrl($url);

        $this->assertSame($url, $this->request->getUrl());
    }

    /**
     * @dataProvider invalidUrlProvider
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSetUrlWithInvalidUrl($url)
    {
        $this->request->setUrl($url);
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
