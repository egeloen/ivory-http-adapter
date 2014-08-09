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

use Ivory\HttpAdapter\Event\PostSendEvent;

/**
 * Post send event test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PostSendEventTest extends AbstractEventTest
{
    /** @var \Ivory\HttpAdapter\Message\ResponseInterface[\PHPUnit_Framework_MockObject_MockObject */
    protected $response;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->response = $this->createResponseMock();

        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->response);

        parent::tearDown();
    }

    public function testGetResponse()
    {
        $this->assertSame($this->response, $this->event->getResponse());
    }

    /**
     * {@inheritdoc}
     */
    protected function createEvent()
    {
        return new PostSendEvent($this->request, $this->response);
    }

    /**
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface[\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    protected function createResponseMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }
}
