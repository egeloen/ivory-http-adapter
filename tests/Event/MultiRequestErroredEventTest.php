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

use Ivory\HttpAdapter\Event\MultiRequestErroredEvent;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class MultiRequestErroredEventTest extends AbstractEventTest
{
    /**
     * @var array
     */
    private $exceptions;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->exceptions = [$this->createExceptionMock()];

        parent::setUp();
    }

    public function testDefaultState()
    {
        parent::testDefaultState();

        $this->assertExceptions($this->exceptions);
        $this->assertNoResponses();
    }

    public function testSetExceptions()
    {
        $this->event->setExceptions($exceptions = [$this->createExceptionMock()]);

        $this->assertExceptions($exceptions);
    }

    public function testAddExceptions()
    {
        $this->event->setExceptions($exceptions = [$this->createExceptionMock()]);
        $this->event->addExceptions($newExceptions = [$this->createExceptionMock()]);

        $this->assertExceptions(array_merge($exceptions, $newExceptions));
    }

    public function testRemoveExceptions()
    {
        $this->event->setExceptions($exceptions = [$this->createExceptionMock()]);
        $this->event->removeExceptions($exceptions);

        $this->assertNoExceptions();
    }

    public function testClearExceptions()
    {
        $this->event->setExceptions([$this->createExceptionMock()]);
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

    public function testSetResponses()
    {
        $this->event->setResponses($responses = [$this->createResponseMock()]);

        $this->assertResponses($responses);
    }

    public function testAddResponses()
    {
        $this->event->setResponses($responses = [$this->createResponseMock()]);
        $this->event->addResponses($newResponses = [$this->createResponseMock()]);

        $this->assertResponses(array_merge($responses, $newResponses));
    }

    public function testRemoveResponses()
    {
        $this->event->setResponses($responses = [$this->createResponseMock()]);
        $this->event->removeResponses($responses);

        $this->assertNoResponses();
    }

    public function testClearResponses()
    {
        $this->event->setResponses([$this->createResponseMock()]);
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

    /**
     * {@inheritdoc}
     */
    protected function createEvent()
    {
        return new MultiRequestErroredEvent($this->httpAdapter, $this->exceptions);
    }

    /**
     * @return HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createExceptionMock()
    {
        return $this->createMock('Ivory\HttpAdapter\HttpAdapterException');
    }

    /**
     * @return ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createResponseMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * @param array $exceptions
     */
    private function assertExceptions(array $exceptions)
    {
        $this->assertTrue($this->event->hasExceptions());
        $this->assertSame($exceptions, $this->event->getExceptions());

        foreach ($exceptions as $exception) {
            $this->assertException($exception);
        }
    }

    private function assertNoExceptions()
    {
        $this->assertFalse($this->event->hasExceptions());
        $this->assertEmpty($this->event->getExceptions());
    }

    /**
     * @param HttpAdapterException $exception
     */
    private function assertException($exception)
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\HttpAdapterException', $exception);
        $this->assertTrue($this->event->hasException($exception));
    }

    /**
     * @param string $exception
     */
    private function assertNoException($exception)
    {
        $this->assertFalse($this->event->hasException($exception));
    }

    /**
     * @param array $responses
     */
    private function assertResponses(array $responses)
    {
        $this->assertTrue($this->event->hasResponses());
        $this->assertSame($responses, $this->event->getResponses());

        foreach ($responses as $response) {
            $this->assertResponse($response);
        }
    }

    private function assertNoResponses()
    {
        $this->assertFalse($this->event->hasResponses());
        $this->assertEmpty($this->event->getResponses());
    }

    /**
     * @param ResponseInterface $response
     */
    private function assertResponse($response)
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\ResponseInterface', $response);
        $this->assertTrue($this->event->hasResponse($response));
    }

    /**
     * @param string $response
     */
    private function assertNoResponse($response)
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\ResponseInterface', $response);
        $this->assertFalse($this->event->hasResponse($response));
    }
}
