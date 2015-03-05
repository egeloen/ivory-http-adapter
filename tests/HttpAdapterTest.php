<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter;

use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\ExceptionEvent;
use Ivory\HttpAdapter\Event\MultiExceptionEvent;
use Ivory\HttpAdapter\Event\MultiPostSendEvent;
use Ivory\HttpAdapter\Event\MultiPreSendEvent;
use Ivory\HttpAdapter\Event\PostSendEvent;
use Ivory\HttpAdapter\Event\PreSendEvent;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\MultiHttpAdapterException;

/**
 * Http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpAdapterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\AbstractHttpAdapter|\PHPUnit_Framework_MockObject_MockObject */
    private $httpAdapter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->httpAdapter = $this->createHttpAdapterMockBuilder()->getMockForAbstractClass();
    }

    public function testVersion()
    {
        $this->assertRegExp('/\d+\.\d+\.\d+(-(.?))?/', HttpAdapterInterface::VERSION);
    }

    public function testVersionId()
    {
        $this->assertRegExp('/\d+\d+\d+\d+\d+/', HttpAdapterInterface::VERSION_ID);
    }

    public function testMajorVersion()
    {
        $this->assertRegExp('/\d+/', HttpAdapterInterface::MAJOR_VERSION);
    }

    public function testMinorVersion()
    {
        $this->assertRegExp('/\d+/', HttpAdapterInterface::MINOR_VERSION);
    }

    public function testPatchVersion()
    {
        $this->assertRegExp('/\d+/', HttpAdapterInterface::PATCH_VERSION);
    }

    public function testExtraVersion()
    {
        $this->assertRegExp('/.?/', HttpAdapterInterface::EXTRA_VERSION);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->httpAdapter);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Configuration', $this->httpAdapter->getConfiguration());
    }

    public function testInitialState()
    {
        $this->httpAdapter = $this->createHttpAdapterMockBuilder()
            ->setConstructorArgs(array($configuration = $this->createConfigurationMock()))
            ->getMockForAbstractClass();

        $this->assertSame($configuration, $this->httpAdapter->getConfiguration());
    }

    public function testSetConfiguration()
    {
        $this->httpAdapter->setConfiguration($configuration = $this->createConfigurationMock());

        $this->assertSame($configuration, $this->httpAdapter->getConfiguration());
    }

    public function testSendRequestWithDisabledEventDispatcher()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest = $this->createInternalRequestMock()))
            ->will($this->returnValue($response = $this->createResponseMock()));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(null);

        $this->assertSame($response, $this->httpAdapter->sendRequest($internalRequest));
    }

    public function testSendRequestWithDisabledEventDispatcherThrowException()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest = $this->createInternalRequestMock()))
            ->will($this->throwException($exception = $this->createExceptionMock()));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(null);

        try {
            $this->httpAdapter->sendRequest($internalRequest);
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertSame($e, $exception);
        }
    }

    public function testSendRequestDispatchPreSendEvent()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $internalRequestOverride = $this->createInternalRequestMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequestOverride))
            ->will($this->returnValue($response = $this->createResponseMock()));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::PRE_SEND),
                $this->callback(function ($event) use ($httpAdapter, $internalRequest, $internalRequestOverride) {
                    $result =  $event instanceof PreSendEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getRequest() === $internalRequest;

                    $event->setRequest($internalRequestOverride);

                    return $result;
                })
            );

        $this->assertSame($response, $this->httpAdapter->sendRequest($internalRequest));
    }

    public function testSendRequestDispatchPostSendEvent()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequest = $this->createInternalRequestMock();
        $response = $this->createResponseMock();
        $responseOverride = $this->createResponseMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest))
            ->will($this->returnValue($response));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::POST_SEND),
                $this->callback(function ($event) use ($httpAdapter, $internalRequest, $response, $responseOverride) {
                    $result = $event instanceof PostSendEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getRequest() === $internalRequest
                        && $event->getResponse() === $response;

                    $event->setResponse($responseOverride);

                    return $result;
                })
            );

        $this->assertSame($responseOverride, $this->httpAdapter->sendRequest($internalRequest));
    }

    public function testSendRequestDispatchExceptionEventAndReturnResponse()
    {
        $httpAdapter = $this->httpAdapter;
        $response = $this->createResponseMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest = $this->createInternalRequestMock()))
            ->will($this->throwException($exception = $this->createExceptionMock()));

        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($internalRequest));

        $exception
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->isNull());

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::EXCEPTION),
                $this->callback(function ($event) use ($httpAdapter, $exception, $response) {
                    $result = $event instanceof ExceptionEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getException() === $exception;

                    $event->setResponse($response);

                    return $result;
                })
            );

        $this->assertSame($response, $this->httpAdapter->sendRequest($internalRequest));
    }

    public function testSendRequestDispatchExceptionEventWhenDoSendThrowException()
    {
        $httpAdapter = $this->httpAdapter;
        $exceptionOverride = $this->createExceptionMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest = $this->createInternalRequestMock()))
            ->will($this->throwException($exception = $this->createExceptionMock()));

        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($internalRequest));

        $exception
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->isNull());

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::EXCEPTION),
                $this->callback(function ($event) use ($httpAdapter, $exception, $exceptionOverride) {
                    $result = $event instanceof ExceptionEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getException() === $exception;

                    $event->setException($exceptionOverride);

                    return $result;
                })
            );

        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($internalRequest));

        $exception
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->isNull());

        try {
            $this->httpAdapter->sendRequest($internalRequest);
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertSame($e, $exceptionOverride);
        }
    }

    public function testSendRequestDispatchExceptionEventWhenPostSendEventHasException()
    {
        $httpAdapter = $this->httpAdapter;
        $exception = $this->createExceptionMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest = $this->createInternalRequestMock()))
            ->will($this->returnValue($response = $this->createResponseMock()));

        $exception
            ->expects($this->once())
            ->method('setRequest')
            ->with($this->identicalTo($internalRequest));

        $exception
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->identicalTo($response));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::POST_SEND),
                $this->callback(function ($event) use ($exception) {
                    $event->setException($exception);

                    return true;
                })
            );

        $eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::EXCEPTION),
                $this->callback(function ($event) use ($httpAdapter, $exception) {
                    return $event instanceof ExceptionEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getException() === $exception;
                })
            );

        try {
            $this->httpAdapter->sendRequest($internalRequest);
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertSame($e, $exception);
        }
    }

    public function testSendRequestsWithDisabledEventDispatcher()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest = $this->createInternalRequestMock()))
            ->will($this->returnValue($response = $this->createResponseMock()));

        $response
            ->expects($this->once())
            ->method('withParameter')
            ->with($this->identicalTo('request'), $this->identicalTo($internalRequest))
            ->will($this->returnValue($response));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(null);

        $this->assertSame(array($response), $this->httpAdapter->sendRequests(array($internalRequest)));
    }

    public function testSendRequestsWithDisabledEventDispatcherThrowException()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest = $this->createInternalRequestMock()))
            ->will($this->throwException($exception = $this->createExceptionMock()));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(null);

        try {
            $this->httpAdapter->sendRequests(array($internalRequest));
            $this->fail();
        } catch (MultiHttpAdapterException $e) {
            $exceptions = $e->getExceptions();
            $this->assertCount(1, $exceptions);
            $this->assertArrayHasKey(0, $exceptions);
            $this->assertSame($exception, $exceptions[0]);
        }
    }

    public function testSendRequestsDispatchMultiPreSendEvent()
    {
        $httpAdapter = $this->httpAdapter;
        $internalRequests = array($this->createInternalRequestMock());

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequestOverride = $this->createInternalRequestMock()))
            ->will($this->returnValue($response = $this->createResponseMock()));

        $response
            ->expects($this->once())
            ->method('withParameter')
            ->with($this->identicalTo('request'), $this->identicalTo($internalRequestOverride))
            ->will($this->returnValue($response));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::MULTI_PRE_SEND),
                $this->callback(function ($event) use ($httpAdapter, $internalRequests, $internalRequestOverride) {
                    $result =  $event instanceof MultiPreSendEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getRequests() === $internalRequests;

                    $event->setRequests(array($internalRequestOverride));

                    return $result;
                })
            );

        $this->assertSame(array($response), $this->httpAdapter->sendRequests($internalRequests));
    }

    public function testSendRequestsDispatchMultiPostSendEvent()
    {
        $httpAdapter = $this->httpAdapter;
        $responseOverride = $this->createResponseMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest = $this->createInternalRequestMock()))
            ->will($this->returnValue($response = $this->createResponseMock()));

        $response
            ->expects($this->once())
            ->method('withParameter')
            ->with($this->identicalTo('request'), $this->identicalTo($internalRequest))
            ->will($this->returnValue($response));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::MULTI_POST_SEND),
                $this->callback(function ($event) use ($httpAdapter, $response, $responseOverride) {
                    $result = $event instanceof MultiPostSendEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getResponses() === array($response)
                        && !$event->hasExceptions();

                    $event->setResponses(array($responseOverride));

                    return $result;
                })
            );

        $this->assertSame(array($responseOverride), $this->httpAdapter->sendRequests(array($internalRequest)));
    }

    public function testSendRequestsDispatchMultiExceptionEventAndReturnResponses()
    {
        $httpAdapter = $this->httpAdapter;
        $responses = array($this->createResponseMock());

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest = $this->createInternalRequestMock()))
            ->will($this->throwException($exception = $this->createExceptionMock()));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::MULTI_EXCEPTION),
                $this->callback(function ($event) use ($httpAdapter, $exception, $responses) {
                    $result = $event instanceof MultiExceptionEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getExceptions() === array($exception)
                        && !$event->hasResponses();

                    $event->setExceptions(array());
                    $event->setResponses($responses);

                    return $result;
                })
            );

        $this->assertSame($responses, $this->httpAdapter->sendRequests(array($internalRequest)));
    }

    public function testSendRequestsDispatchMultiExceptionEventWhenDoSendThrowException()
    {
        $httpAdapter = $this->httpAdapter;
        $exceptionOverride = $this->createExceptionMock();

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest = $this->createInternalRequestMock()))
            ->will($this->throwException($exception = $this->createExceptionMock()));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::MULTI_EXCEPTION),
                $this->callback(function ($event) use ($httpAdapter, $exception, $exceptionOverride) {
                    $result = $event instanceof MultiExceptionEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getExceptions() === array($exception)
                        && !$event->hasResponses();

                    $event->setExceptions(array($exceptionOverride));

                    return $result;
                })
            );

        try {
            $this->httpAdapter->sendRequests(array($internalRequest));
            $this->fail();
        } catch (MultiHttpAdapterException $e) {
            $this->assertSame($e->getExceptions(), array($exceptionOverride));
            $this->assertFalse($e->hasResponses());
        }
    }

    public function testSendRequestsDispatchMultiExceptionEventWhenMultiPostSendEventHasExceptions()
    {
        $httpAdapter = $this->httpAdapter;
        $exceptions = array($this->createExceptionMock());

        $this->httpAdapter
            ->expects($this->once())
            ->method('doSendInternalRequest')
            ->with($this->identicalTo($internalRequest = $this->createInternalRequestMock()))
            ->will($this->returnValue($response = $this->createResponseMock()));

        $response
            ->expects($this->once())
            ->method('withParameter')
            ->with($this->identicalTo('request'), $this->identicalTo($internalRequest))
            ->will($this->returnValue($response));

        $this->httpAdapter->getConfiguration()->setEventDispatcher(
            $eventDispatcher = $this->createEventDispatcherMock()
        );

        $eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::MULTI_POST_SEND),
                $this->callback(function ($event) use ($exceptions) {
                    $event->setExceptions($exceptions);

                    return true;
                })
            );

        $eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo(Events::MULTI_EXCEPTION),
                $this->callback(function ($event) use ($httpAdapter, $exceptions) {
                    return $event instanceof MultiExceptionEvent
                        && $event->getHttpAdapter() === $httpAdapter
                        && $event->getExceptions() === $exceptions;
                })
            );

        try {
            $this->httpAdapter->sendRequests(array($internalRequest));
            $this->fail();
        } catch (MultiHttpAdapterException $e) {
            $this->assertSame($e->getExceptions(), $exceptions);
            $this->assertTrue($e->hasResponses(), array($response));
        }
    }

    public function testSendRequestsWithInvalidRequests()
    {
        try {
            $this->httpAdapter->sendRequests(array(true));
            $this->fail();
        } catch (MultiHttpAdapterException $e) {
            $this->assertFalse($e->hasResponses());

            $exceptions = $e->getExceptions();

            $this->assertCount(1, $exceptions);
            $this->assertArrayHasKey(0, $exceptions);
            $this->assertInstanceOf('Ivory\HttpAdapter\HttpAdapterException', $exceptions[0]);

            $this->assertSame(
                'The request must be a string, an array or implement "Psr\Http\Message\RequestInterface" ("boolean" given).',
                $exceptions[0]->getMessage()
            );

            $this->assertFalse($exceptions[0]->hasRequest());
            $this->assertFalse($exceptions[0]->hasResponse());
        }
    }

    /**
     * Creates an http adapter mock builder.
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder The http adapter mock builder.
     */
    private function createHttpAdapterMockBuilder()
    {
        return $this->getMockBuilder('Ivory\HttpAdapter\AbstractHttpAdapter');
    }

    /**
     * Creates a message factory mock.
     *
     * @return \Ivory\HttpAdapter\Message\MessageFactoryInterface|\PHPUnit_Framework_MockObject_MockObject The message factory mock.
     */
    private function createMessageFactoryMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\MessageFactoryInterface');
    }

    /**
     * Creates a configuration mock.
     *
     * @return \Ivory\HttpAdapter\ConfigurationInterface|\PHPUnit_Framework_MockObject_MockObject The configuration mock.
     */
    private function createConfigurationMock()
    {
        return $this->getMock('Ivory\HttpAdapter\ConfigurationInterface');
    }

    /**
     * Creates an event dispatcher mock.
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject The event dispatcher mock.
     */
    private function createEventDispatcherMock()
    {
        return $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }

    /**
     * Creates an internal request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The internal request mock.
     */
    private function createInternalRequestMock()
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
     * Creates a multi exception mock.
     *
     * @param array $exceptions The exceptions.
     * @param array $responses  The responses.
     *
     * @return \Ivory\HttpAdapter\MultiHttpAdapterException|\PHPUnit_Framework_MockObject_MockObject The multi exception mock.
     */
    private function createMultiExceptionMock(array $exceptions = array(), array $responses = array())
    {
        $exception = $this->getMock('Ivory\HttpAdapter\MultiHttpAdapterException');

        if (empty($exceptions)) {
            $exceptions[] = $this->createExceptionMock();
        }

        $exception
            ->expects($this->any())
            ->method('hasExceptions')
            ->will($this->returnValue(true));

        $exception
            ->expects($this->any())
            ->method('getExceptions')
            ->will($this->returnValue($exceptions));

        $exception
            ->expects($this->any())
            ->method('hasResponses')
            ->will($this->returnValue(!empty($responses)));

        $exception
            ->expects($this->any())
            ->method('getResponses')
            ->will($this->returnValue($responses));

        return $exception;
    }
}
