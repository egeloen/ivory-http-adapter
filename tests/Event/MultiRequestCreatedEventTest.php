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

use Ivory\HttpAdapter\Event\MultiRequestCreatedEvent;

/**
 * Multi pre send event test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MultiRequestCreatedEventTest extends AbstractEventTest
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
        $this->assertNoResponses();
        $this->assertNoExceptions();
    }

    public function testInitialState()
    {
        $this->event = new MultiRequestCreatedEvent($this->httpAdapter, $requests = array());

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

    public function testSetResponses()
    {
        $this->event->setResponses($responses = array($this->createResponseMock()));

        $this->assertResponses($responses);
    }

    public function testAddResponses()
    {
        $this->event->setResponses($responses = array($this->createResponseMock()));
        $this->event->addResponses($newResponses = array($this->createResponseMock()));

        $this->assertResponses(array_merge($responses, $newResponses));
    }

    public function testRemoveResponses()
    {
        $this->event->setResponses($responses = array($this->createResponseMock()));
        $this->event->removeResponses($responses);

        $this->assertNoResponses();
    }

    public function testClearResponses()
    {
        $this->event->setResponses(array($this->createResponseMock()));
        $this->event->clearResponses();

        $this->assertNoResponses();
    }

    public function testAddResponse()
    {
        $this->event->addResponse($response = $this->createResponseMock());

        $this->assertResponse($response);
    }

    public function testRemoveResponse()
    {
        $this->event->addResponse($response = $this->createResponseMock());
        $this->event->removeResponse($response);

        $this->assertNoResponse($response);
    }

    public function testSetExceptions()
    {
        $this->event->setExceptions($exceptions = array($this->createExceptionMock()));

        $this->assertExceptions($exceptions);
    }

    public function testAddExceptions()
    {
        $this->event->setExceptions($exceptions = array($this->createExceptionMock()));
        $this->event->addExceptions($newExceptions = array($this->createExceptionMock()));

        $this->assertExceptions(array_merge($exceptions, $newExceptions));
    }

    public function testRemoveExceptions()
    {
        $this->event->setExceptions($exceptions = array($this->createExceptionMock()));
        $this->event->removeExceptions($exceptions);

        $this->assertNoExceptions();
    }

    public function testClearExceptions()
    {
        $this->event->setExceptions(array($this->createExceptionMock()));
        $this->event->clearExceptions();

        $this->assertNoExceptions();
    }

    public function testAddException()
    {
        $this->event->addException($exception = $this->createExceptionMock());

        $this->assertException($exception);
    }

    public function testRemoveException()
    {
        $this->event->addException($exception = $this->createExceptionMock());
        $this->event->removeException($exception);

        $this->assertNoException($exception);
    }

    /**
     * {@inheritdoc}
     */
    protected function createEvent()
    {
        return new MultiRequestCreatedEvent($this->httpAdapter, $this->requests);
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
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    private function createResponseMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * Creates an exception mock.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject The exception mock.
     */
    private function createExceptionMock()
    {
        return $this->getMock('Ivory\HttpAdapter\HttpAdapterException');
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

    /**
     * Asserts there are the responses.
     *
     * @param array $responses The responses.
     */
    private function assertResponses(array $responses)
    {
        $this->assertTrue($this->event->hasResponses());
        $this->assertSame($responses, $this->event->getResponses());

        foreach ($responses as $response) {
            $this->assertResponse($response);
        }
    }

    /**
     * Asserts there are no responses.
     */
    private function assertNoResponses()
    {
        $this->assertFalse($this->event->hasResponses());
        $this->assertEmpty($this->event->getResponses());
    }

    /**
     * Asserts there is a response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface $response The response.
     */
    private function assertResponse($response)
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\ResponseInterface', $response);
        $this->assertTrue($this->event->hasResponse($response));
    }

    /**
     * Asserts there is no response.
     *
     * @param string $response The response.
     */
    private function assertNoResponse($response)
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\ResponseInterface', $response);
        $this->assertFalse($this->event->hasResponse($response));
    }

    /**
     * Asserts there are the exceptions.
     *
     * @param array $exceptions The exceptions.
     */
    private function assertExceptions(array $exceptions)
    {
        $this->assertTrue($this->event->hasExceptions());
        $this->assertSame($exceptions, $this->event->getExceptions());

        foreach ($exceptions as $exception) {
            $this->assertException($exception);
        }
    }

    /**
     * Asserts there are no exceptions.
     */
    private function assertNoExceptions()
    {
        $this->assertFalse($this->event->hasExceptions());
        $this->assertEmpty($this->event->getExceptions());
    }

    /**
     * Asserts there is an exception.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterException $exception The exception.
     */
    private function assertException($exception)
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\HttpAdapterException', $exception);
        $this->assertTrue($this->event->hasException($exception));
    }

    /**
     * Asserts there is no exception.
     *
     * @param string $exception The exception.
     */
    private function assertNoException($exception)
    {
        $this->assertFalse($this->event->hasException($exception));
    }
}
