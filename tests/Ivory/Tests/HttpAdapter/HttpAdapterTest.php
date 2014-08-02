<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter;

use Ivory\HttpAdapter\HttpAdapterConfigInterface;
use Ivory\HttpAdapter\Message\MessageInterface;

/**
 * Http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpAdapterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\AbstractHttpAdapter|\PHPUnit_Framework_MockObject_MockObject */
    protected $httpAdapter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->httpAdapter = $this->createHttpAdapterMockBuilder()->getMockForAbstractClass();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->httpAdapter);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\MessageFactory', $this->httpAdapter->getMessageFactory());
        $this->assertSame(MessageInterface::PROTOCOL_VERSION_11, $this->httpAdapter->getProtocolVersion());
        $this->assertFalse($this->httpAdapter->getKeepAlive());
        $this->assertNull($this->httpAdapter->getEncodingType());
        $this->assertInternalType('string', $this->httpAdapter->getBoundary());
        $this->assertTrue($this->httpAdapter->hasMaxRedirects());
        $this->assertSame(5, $this->httpAdapter->getMaxRedirects());
    }

    public function testInitialState()
    {
        $this->httpAdapter = $this->createHttpAdapterMockBuilder()
            ->setConstructorArgs(array($factory = $this->createFactoryMock()))
            ->getMockForAbstractClass();

        $this->assertSame($factory, $this->httpAdapter->getMessageFactory());
    }

    public function testSetFactory()
    {
        $this->httpAdapter->setMessageFactory($factory = $this->createFactoryMock());

        $this->assertSame($factory, $this->httpAdapter->getMessageFactory());
    }

    public function testSetProtocolVersion()
    {
        $this->httpAdapter->setProtocolVersion($protocolVersion = MessageInterface::PROTOCOL_VERSION_10);

        $this->assertSame($protocolVersion, $this->httpAdapter->getProtocolVersion());
    }

    public function testSetKeepAlive()
    {
        $this->httpAdapter->setKeepAlive(true);

        $this->assertTrue($this->httpAdapter->getKeepAlive());
    }

    public function testSetEncodingType()
    {
        $this->httpAdapter->setEncodingType($encodingType = HttpAdapterConfigInterface::ENCODING_TYPE_FORMDATA);

        $this->assertSame($encodingType, $this->httpAdapter->getEncodingType());
    }

    public function testSetBoundary()
    {
        $this->httpAdapter->setBoundary($boundary = 'foo');

        $this->assertSame($boundary, $this->httpAdapter->getBoundary());
    }

    public function testSetMaxRedirects()
    {
        $this->httpAdapter->setMaxRedirects($maxRedirects = 0);

        $this->assertFalse($this->httpAdapter->hasMaxRedirects());
        $this->assertSame($maxRedirects, $this->httpAdapter->getMaxRedirects());
    }

    /**
     * Creates an http adapter mock builder.
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder The http adapter mock builder.
     */
    protected function createHttpAdapterMockBuilder()
    {
        return $this->getMockBuilder('Ivory\HttpAdapter\AbstractHttpAdapter');
    }

    /**
     * Creates a factory mock.
     *
     * @return \Ivory\HttpAdapter\Message\MessageFactoryInterface|\PHPUnit_Framework_MockObject_MockObject The factory mock.
     */
    protected function createFactoryMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\MessageFactoryInterface');
    }
}
