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

    /** @var \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->httpAdapter = $this->createHttpAdapterMock();
        $this->request = $this->createRequestMock();
        $this->event = $this->createEvent();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->httpAdapter);
        unset($this->request);
        unset($this->event);
    }

    public function testDefaultState()
    {
        $this->assertSame($this->httpAdapter, $this->event->getHttpAdapter());
        $this->assertSame($this->request, $this->event->getRequest());
    }

    public function testSetHttpAdapter()
    {
        $this->event->setHttpAdapter($httpAdapter = $this->createHttpAdapterMock());

        $this->assertSame($httpAdapter, $this->event->getHttpAdapter());
    }

    public function testSetRequest()
    {
        $this->event->setRequest($request = $this->createRequestMock());

        $this->assertSame($request, $this->event->getRequest());
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
    protected function createHttpAdapterMock()
    {
        return $this->getMock('Ivory\HttpAdapter\HttpAdapterInterface');
    }

    /**
     * Creates a request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The request mock.
     */
    protected function createRequestMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }
}
