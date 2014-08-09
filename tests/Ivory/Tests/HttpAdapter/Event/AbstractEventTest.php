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

    /** @var \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->request = $this->createRequestMock();
        $this->event = $this->createEvent();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->request);
        unset($this->event);
    }

    public function testGetRequest()
    {
        $this->assertSame($this->request, $this->event->getRequest());
    }

    /**
     * Creates the event.
     *
     * @return mixed The event.
     */
    abstract protected function createEvent();

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
