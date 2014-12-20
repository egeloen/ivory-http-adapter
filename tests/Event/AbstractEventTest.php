<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event;

/**
 * Abstract event test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractEventTest extends \PHPUnit_Framework_TestCase
{
    /** @var mixed */
    protected $event;

    /** @var \Ivory\HttpAdapter\HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $httpAdapter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->httpAdapter = $this->createHttpAdapterMock();
        $this->event = $this->createEvent();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->httpAdapter);
        unset($this->event);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->httpAdapter, $this->event->getHttpAdapter());
    }

    public function testSetHttpAdapter()
    {
        $this->event->setHttpAdapter($httpAdapter = $this->createHttpAdapterMock());

        $this->assertSame($httpAdapter, $this->event->getHttpAdapter());
    }

    /**
     * Creates the event.
     *
     * @return mixed The event.
     */
    abstract protected function createEvent();

    /**
     * Creates an http adapter mock.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject The http adapter mock.
     */
    private function createHttpAdapterMock()
    {
        return $this->getMock('Ivory\HttpAdapter\HttpAdapterInterface');
    }
}
