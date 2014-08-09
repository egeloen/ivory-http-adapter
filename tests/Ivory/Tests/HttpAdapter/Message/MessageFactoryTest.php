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

use Ivory\HttpAdapter\Message\MessageFactory;
use Ivory\HttpAdapter\Message\RequestInterface;

/**
 * Message factory test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MessageFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Message\MessageFactory */
    protected $messageFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->messageFactory = new MessageFactory();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->messageFactory);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\MessageFactoryInterface', $this->messageFactory);
    }

    public function testCreateRequestWithoutMethod()
    {
        $request = $this->messageFactory->createRequest($url = 'http://egeloen.fr/');

        $this->assertSame($url, $request->getUrl());
        $this->assertSame(RequestInterface::METHOD_GET, $request->getMethod());
    }

    public function testCreateRequestWithMethod()
    {
        $request = $this->messageFactory->createRequest(
            $url = 'http://egeloen.fr/',
            $method = RequestInterface::METHOD_POST
        );

        $this->assertSame($url, $request->getUrl());
        $this->assertSame($method, $request->getMethod());
    }

    public function testCreateInternalRequestWithoutMethod()
    {
        $internalRequest = $this->messageFactory->createInternalRequest($url = 'http://egeloen.fr/');

        $this->assertSame($url, $internalRequest->getUrl());
        $this->assertSame(RequestInterface::METHOD_GET, $internalRequest->getMethod());
    }

    public function testCreateInternalRequestWithMethod()
    {
        $internalRequest = $this->messageFactory->createInternalRequest(
            $url = 'http://egeloen.fr/',
            $method = RequestInterface::METHOD_POST
        );

        $this->assertSame($url, $internalRequest->getUrl());
        $this->assertSame($method, $internalRequest->getMethod());
    }

    public function testCreateResponse()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\Response', $this->messageFactory->createResponse());
    }
}
