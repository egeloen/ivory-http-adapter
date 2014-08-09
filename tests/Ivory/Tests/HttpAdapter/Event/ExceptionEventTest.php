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

use Ivory\HttpAdapter\Event\ExceptionEvent;

/**
 * Exception event test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ExceptionEventTest extends AbstractEventTest
{
    /** @var \Ivory\HttpAdapter\HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject */
    protected $exception;

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

    public function testGetException()
    {
        $this->assertSame($this->exception, $this->event->getException());
    }

    /**
     * {@inheritdoc}
     */
    protected function createEvent()
    {
        return new ExceptionEvent($this->request, $this->exception);
    }

    /**
     * Creates an exception mock.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject The exception mock.
     */
    protected function createExceptionMock()
    {
        return $this->getMock('Ivory\HttpAdapter\HttpAdapterException');
    }
}
