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

use Ivory\HttpAdapter\Event\MultiExceptionEvent;

/**
 * Multi exception event test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MultiExceptionEventTest extends AbstractEventTest
{
    /** @var \Ivory\HttpAdapter\MultiHttpAdapterException|\PHPUnit_Framework_MockObject_MockObject */
    private $exception;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->exception = $this->createExceptionMock();

        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->exception);

        parent::tearDown();
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Event\AbstractEvent', $this->event);
        $this->assertSame($this->httpAdapter, $this->event->getHttpAdapter());
        $this->assertSame($this->exception, $this->event->getException());
    }

    public function testSetException()
    {
        $this->event->setException($exception = $this->createExceptionMock());

        $this->assertSame($exception, $this->event->getException());
    }

    /**
     * {@inheritdoc}
     */
    protected function createEvent()
    {
        return new MultiExceptionEvent($this->httpAdapter, $this->exception);
    }

    /**
     * Creates an exception mock.
     *
     * @return \Ivory\HttpAdapter\MulltiHttpAdapterException|\PHPUnit_Framework_MockObject_MockObject The exception mock.
     */
    private function createExceptionMock()
    {
        return $this->getMock('Ivory\HttpAdapter\MultiHttpAdapterException');
    }
}
