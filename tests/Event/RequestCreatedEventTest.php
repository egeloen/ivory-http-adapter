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

use Ivory\HttpAdapter\Event\RequestCreatedEvent;

/**
 * Pre send event test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RequestCreatedEventTest extends AbstractEventTest
{
    /** @var \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->request = $this->createRequestMock();

        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->request);

        parent::tearDown();
    }

    public function testDefaultState()
    {
        parent::setUp();

        $this->assertSame($this->request, $this->event->getRequest());
    }

    public function testSetRequest()
    {
        $this->event->setRequest($request = $this->createRequestMock());

        $this->assertSame($request, $this->event->getRequest());
    }

    /**
     * {@inheritdoc}
     */
    protected function createEvent()
    {
        return new RequestCreatedEvent($this->httpAdapter, $this->request);
    }

    /**
     * Creates a request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The request mock.
     */
    private function createRequestMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }
}
