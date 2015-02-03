<?php

/**
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event;

use Ivory\HttpAdapter\Event\MultiPreSendEvent;

/**
 * Multi pre send event test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MultiPreSendEventTest extends AbstractEventTest
{
    /** @var array */
    private $requests;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->requests = array($this->createRequestMock());

        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->requests);

        parent::tearDown();
    }

    public function testDefaultState()
    {
        parent::testDefaultState();

        $this->assertRequests($this->requests);
    }

    public function testInitialState()
    {
        $this->event = new MultiPreSendEvent($this->httpAdapter, $requests = array());

        $this->assertNoRequests();
    }

    public function testSetRequests()
    {
        $this->event->setRequests($requests = array($this->createRequestMock()));

        $this->assertRequests($requests);
    }

    public function testAddRequests()
    {
        $this->event->setRequests($requests = array($this->createRequestMock()));
        $this->event->addRequests($newRequests = array($this->createRequestMock()));

        $this->assertRequests(array_merge($requests, $newRequests));
    }

    public function testRemoveRequests()
    {
        $this->event->setRequests($requests = array($this->createRequestMock()));
        $this->event->removeRequests($requests);

        $this->assertNoRequests();
    }

    public function testClearRequests()
    {
        $this->event->setRequests(array($this->createRequestMock()));
        $this->event->clearRequests();

        $this->assertNoRequests();
    }

    public function testAddRequest()
    {
        $this->event->addRequest($request = $this->createRequestMock());

        $this->assertRequest($request);
    }

    public function testRemoveRequest()
    {
        $this->event->addRequest($request = $this->createRequestMock());
        $this->event->removeRequest($request);

        $this->assertNoRequest($request);
    }

    /**
     * {@inheritdoc}
     */
    protected function createEvent()
    {
        return new MultiPreSendEvent($this->httpAdapter, $this->requests);
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

    /**
     * Asserts there are the requests.
     *
     * @param array $requests The requests.
     */
    private function assertRequests(array $requests)
    {
        $this->assertTrue($this->event->hasRequests());
        $this->assertSame($requests, $this->event->getRequests());

        foreach ($requests as $request) {
            $this->assertRequest($request);
        }
    }

    /**
     * Asserts there are no requests.
     */
    private function assertNoRequests()
    {
        $this->assertFalse($this->event->hasRequests());
        $this->assertEmpty($this->event->getRequests());
    }

    /**
     * Asserts there is a request.
     *
     * @param \Ivory\HttpAdapter\Message\RequestInterface $request The request.
     */
    private function assertRequest($request)
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\RequestInterface', $request);
        $this->assertTrue($this->event->hasRequest($request));
    }

    /**
     * Asserts there is no request.
     *
     * @param string $request The request.
     */
    private function assertNoRequest($request)
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\RequestInterface', $request);
        $this->assertFalse($this->event->hasRequest($request));
    }
}
