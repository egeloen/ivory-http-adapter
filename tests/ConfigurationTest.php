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

use Ivory\HttpAdapter\Configuration;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * Configuration test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Configuration */
    protected $configuration;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->configuration = new Configuration();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->configuration);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\MessageFactory', $this->configuration->getMessageFactory());

        $this->assertInstanceOf(
            'Symfony\Component\EventDispatcher\EventDispatcher',
            $this->configuration->getEventDispatcher()
        );

        $this->assertSame(InternalRequestInterface::PROTOCOL_VERSION_1_1, $this->configuration->getProtocolVersion());
        $this->assertFalse($this->configuration->getKeepAlive());
        $this->assertFalse($this->configuration->hasEncodingType());
        $this->assertInternalType('string', $this->configuration->getBoundary());
        $this->assertSame(10, $this->configuration->getTimeout());
        $this->assertSame('Ivory Http Adapter', $this->configuration->getUserAgent());
    }

    public function testInitialState()
    {
        $this->configuration = new Configuration(
            $messageFactory = $this->createMessageFactoryMock(),
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $this->assertSame($messageFactory, $this->configuration->getMessageFactory());
        $this->assertSame($eventDispatcher, $this->configuration->getEventDispatcher());
    }

    public function testSetMessageFactory()
    {
        $this->configuration->setMessageFactory($messageFactory = $this->createMessageFactoryMock());

        $this->assertSame($messageFactory, $this->configuration->getMessageFactory());
    }

    public function testSetEventDispatcher()
    {
        $this->configuration->setEventDispatcher($eventDispatcher = $this->createEventDispatcherMock());

        $this->assertSame($eventDispatcher, $this->configuration->getEventDispatcher());
    }

    public function testSetProtocolVersion()
    {
        $this->configuration->setProtocolVersion($protocolVersion = InternalRequestInterface::PROTOCOL_VERSION_1_0);

        $this->assertSame($protocolVersion, $this->configuration->getProtocolVersion());
    }

    public function testSetKeepAlive()
    {
        $this->configuration->setKeepAlive(true);

        $this->assertTrue($this->configuration->getKeepAlive());
    }

    public function testSetEncodingType()
    {
        $this->configuration->setEncodingType($encodingType = Configuration::ENCODING_TYPE_FORMDATA);

        $this->assertSame($encodingType, $this->configuration->getEncodingType());
    }

    public function testSetBoundary()
    {
        $this->configuration->setBoundary($boundary = 'foo');

        $this->assertSame($boundary, $this->configuration->getBoundary());
    }

    public function testSetTimeout()
    {
        $this->configuration->setTimeout($timeout = 2.5);

        $this->assertSame($timeout, $this->configuration->getTimeout());
    }

    public function testSetUserAgent()
    {
        $this->configuration->setUserAgent($userAgent = 'foo');

        $this->assertSame($userAgent, $this->configuration->getUserAgent());
    }

    /**
     * Creates a message factory mock.
     *
     * @return \Ivory\HttpAdapter\Message\MessageFactoryInterface|\PHPUnit_Framework_MockObject_MockObject The message factory mock.
     */
    protected function createMessageFactoryMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\MessageFactoryInterface');
    }

    /**
     * Creates an event dispatcher mock.
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject The event dispatcher mock.
     */
    protected function createEventDispatcherMock()
    {
        return $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }
}
