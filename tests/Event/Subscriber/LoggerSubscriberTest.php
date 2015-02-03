<?php

/**
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Subscriber;

use Ivory\HttpAdapter\Event\Events;
use Ivory\HttpAdapter\Event\Subscriber\LoggerSubscriber;
use Ivory\HttpAdapter\Event\Timer\TimerInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * Logger subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class LoggerSubscriberTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\LoggerSubscriber */
    private $loggerSubscriber;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    /** @var \Ivory\HttpAdapter\Event\Formatter\FormatterInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $formatter;

    /** @var \Ivory\HttpAdapter\Event\Timer\TimerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $timer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->loggerSubscriber = new LoggerSubscriber(
            $this->logger = $this->createLoggerMock(),
            $this->formatter = $this->createFormatterMock(),
            $this->timer = $this->createTimerMock()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->timer);
        unset($this->formatter);
        unset($this->logger);
        unset($this->loggerSubscriber);
    }

    public function testDefaultState()
    {
        $this->loggerSubscriber = new LoggerSubscriber($this->logger);

        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Subscriber\AbstractFormatterSubscriber',
            $this->loggerSubscriber
        );

        $this->assertSame($this->logger, $this->loggerSubscriber->getLogger());

        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Formatter\Formatter',
            $this->loggerSubscriber->getFormatter()
        );

        $this->assertInstanceOf('Ivory\HttpAdapter\Event\Timer\Timer', $this->loggerSubscriber->getTimer());
    }

    public function testInitialState()
    {
        $this->assertSame($this->logger, $this->loggerSubscriber->getLogger());
        $this->assertSame($this->formatter, $this->loggerSubscriber->getFormatter());
        $this->assertSame($this->timer, $this->loggerSubscriber->getTimer());
    }

    public function testSetLogger()
    {
        $this->loggerSubscriber->setLogger($logger = $this->createLoggerMock());

        $this->assertSame($logger, $this->loggerSubscriber->getLogger());
    }

    public function testSubscribedEvents()
    {
        $events = LoggerSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(Events::PRE_SEND, $events);
        $this->assertSame(array('onPreSend', 100), $events[Events::PRE_SEND]);

        $this->assertArrayHasKey(Events::POST_SEND, $events);
        $this->assertSame(array('onPostSend', 100), $events[Events::POST_SEND]);

        $this->assertArrayHasKey(Events::EXCEPTION, $events);
        $this->assertSame(array('onException', 100), $events[Events::EXCEPTION]);

        $this->assertArrayHasKey(Events::MULTI_PRE_SEND, $events);
        $this->assertSame(array('onMultiPreSend', 100), $events[Events::MULTI_PRE_SEND]);

        $this->assertArrayHasKey(Events::MULTI_POST_SEND, $events);
        $this->assertSame(array('onMultiPostSend', 100), $events[Events::MULTI_POST_SEND]);

        $this->assertArrayHasKey(Events::MULTI_EXCEPTION, $events);
        $this->assertSame(array('onMultiException', 100), $events[Events::MULTI_EXCEPTION]);
    }

    public function testPostSendEvent()
    {
        $this->timer
            ->expects($this->once())
            ->method('start')
            ->with($this->identicalTo($request = $this->createRequestMock()));

        $this->timer
            ->expects($this->once())
            ->method('stop')
            ->with($this->identicalTo($request));

        $this->formatter
            ->expects($this->once())
            ->method('formatRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($formattedRequest = 'request'));

        $this->formatter
            ->expects($this->once())
            ->method('formatResponse')
            ->with($this->identicalTo($response = $this->createResponseMock()))
            ->will($this->returnValue($formattedResponse = 'response'));

        $this->logger
            ->expects($this->once())
            ->method('debug')
            ->with(
                $this->matchesRegularExpression('/^Send "GET http:\/\/egeloen\.fr" in [0-9]+\.[0-9]{2} ms\.$/'),
                $this->identicalTo(array(
                    'adapter'  => 'http_adapter',
                    'request'  => $formattedRequest,
                    'response' => $formattedResponse,
                ))
            );

        $this->loggerSubscriber->onPreSend($this->createPreSendEvent(null, $request));
        $this->loggerSubscriber->onPostSend($this->createPostSendEvent(null, $request, $response));
    }

    public function testExceptionEvent()
    {
        $this->timer
            ->expects($this->once())
            ->method('start')
            ->with($this->identicalTo($request = $this->createRequestMock()));

        $this->timer
            ->expects($this->once())
            ->method('stop')
            ->with($this->identicalTo($request));

        $this->formatter
            ->expects($this->once())
            ->method('formatRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($formattedRequest = 'request'));

        $this->formatter
            ->expects($this->once())
            ->method('formatResponse')
            ->with($this->identicalTo($response = $this->createResponseMock()))
            ->will($this->returnValue($formattedResponse = 'response'));

        $this->formatter
            ->expects($this->once())
            ->method('formatException')
            ->with($this->identicalTo($exception = $this->createExceptionMock($request, $response)))
            ->will($this->returnValue($formattedException = 'exception'));

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with(
                $this->identicalTo('Unable to send "GET http://egeloen.fr".'),
                $this->identicalTo(array(
                    'adapter'   => 'http_adapter',
                    'exception' => $formattedException,
                    'request'   => $formattedRequest,
                    'response'  => $formattedResponse,
                ))
            );

        $this->loggerSubscriber->onPreSend($this->createPreSendEvent(null, $request));
        $this->loggerSubscriber->onException($this->createExceptionEvent(null, $exception));
    }

    public function testMultiPostSendEvent()
    {
        $requests = array($request1 = $this->createRequestMock(), $request2 = $this->createRequestMock());

        $responses = array(
            $response1 = $this->createResponseMock($request1),
            $response2 = $this->createResponseMock($request2),
        );

        $this->timer
            ->expects($this->exactly(count($responses)))
            ->method('start')
            ->withConsecutive(array($request1), array($request2));

        $this->timer
            ->expects($this->exactly(count($responses)))
            ->method('stop')
            ->withConsecutive(array($request1), array($request2));

        $this->formatter
            ->expects($this->exactly(count($responses)))
            ->method('formatRequest')
            ->will($this->returnValueMap(array(
                array($request1, $formattedRequest1 = 'request1'),
                array($request2, $formattedRequest2 = 'request2'),
            )));

        $this->formatter
            ->expects($this->exactly(count($responses)))
            ->method('formatResponse')
            ->will($this->returnValueMap(array(
                array($response1, $formattedResponse1 = 'response1'),
                array($response2, $formattedResponse2 = 'response2'),
            )));

        $this->logger
            ->expects($this->exactly(count($responses)))
            ->method('debug')
            ->withConsecutive(
                array(
                    $this->matchesRegularExpression('/^Send "GET http:\/\/egeloen\.fr" in [0-9]+\.[0-9]{2} ms\.$/'),
                    array(
                        'adapter'  => 'http_adapter',
                        'request'  => $formattedRequest1,
                        'response' => $formattedResponse1,
                    ),
                ),
                array(
                    $this->matchesRegularExpression('/^Send "GET http:\/\/egeloen\.fr" in [0-9]+\.[0-9]{2} ms\.$/'),
                    array(
                        'adapter'  => 'http_adapter',
                        'request'  => $formattedRequest2,
                        'response' => $formattedResponse2,
                    ),
                )
            );

        $this->loggerSubscriber->onMultiPreSend($this->createMultiPreSendEvent(null, $requests));
        $this->loggerSubscriber->onMultiPostSend($this->createMultiPostSendEvent(null, $responses));
    }

    public function testMultiExceptionEvent()
    {
        $requests = array(
            $request1 = $this->createRequestMock(),
            $request2 = $this->createRequestMock(),
        );

        $exceptions = array(
            $exception1 = $this->createExceptionMock($request1, $response1 = $this->createResponseMock($request1)),
            $exception2 = $this->createExceptionMock($request2, $response2 = $this->createResponseMock($request2)),
        );

        $this->timer
            ->expects($this->exactly(count($exceptions)))
            ->method('start')
            ->withConsecutive(array($request1), array($request2));

        $this->timer
            ->expects($this->exactly(count($exceptions)))
            ->method('stop')
            ->withConsecutive(array($request1), array($request2));

        $this->formatter
            ->expects($this->exactly(count($exceptions)))
            ->method('formatRequest')
            ->will($this->returnValueMap(array(
                array($request1, $formattedRequest1 = 'request1'),
                array($request2, $formattedRequest2 = 'request2'),
            )));

        $this->formatter
            ->expects($this->exactly(count($exceptions)))
            ->method('formatResponse')
            ->will($this->returnValueMap(array(
                array($response1, $formattedResponse1 = 'response1'),
                array($response2, $formattedResponse2 = 'response2'),
            )));

        $this->formatter
            ->expects($this->exactly(count($exceptions)))
            ->method('formatException')
            ->will($this->returnValueMap(array(
                array($exception1, $formattedException1 = 'exception1'),
                array($exception2, $formattedException2 = 'exception1'),
            )));

        $this->logger
            ->expects($this->exactly(count($exceptions)))
            ->method('error')
            ->withConsecutive(
                array(
                    $this->identicalTo('Unable to send "GET http://egeloen.fr".'),
                    $this->identicalTo(array(
                        'adapter'   => 'http_adapter',
                        'exception' => $formattedException1,
                        'request'   => $formattedRequest1,
                        'response'  => $formattedResponse1,
                    )),
                ),
                array(
                    $this->identicalTo('Unable to send "GET http://egeloen.fr".'),
                    $this->identicalTo(array(
                        'adapter'   => 'http_adapter',
                        'exception' => $formattedException2,
                        'request'   => $formattedRequest2,
                        'response'  => $formattedResponse2,
                    )),
                )
            );

        $this->loggerSubscriber->onMultiPreSend($this->createMultiPreSendEvent(null, $requests));
        $this->loggerSubscriber->onMultiException($this->createMultiExceptionEvent(null, $exceptions));
    }

    /**
     * {@inheritdoc}
     */
    protected function createRequestMock()
    {
        $request = parent::createRequestMock();

        $request
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue('http://egeloen.fr'));

        $request
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('GET'));

        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(TimerInterface::TIME))
            ->will($this->returnValue(123));

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    protected function createResponseMock(InternalRequestInterface $internalRequest = null)
    {
        $response = parent::createResponseMock();
        $response
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo('request'))
            ->will($this->returnValue($internalRequest));

        return $response;
    }

    /**
     * Creates a logger mock.
     *
     * @return \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject The logger mock.
     */
    private function createLoggerMock()
    {
        return $this->getMock('Psr\Log\LoggerInterface');
    }

    /**
     * Creates a formatter mock.
     *
     * @return \Ivory\HttpAdapter\Event\Formatter\FormatterInterface|\PHPUnit_Framework_MockObject_MockObject The formatter mock.
     */
    private function createFormatterMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\Formatter\FormatterInterface');
    }

    /**
     * Creates a timer mock.
     *
     * @return \Ivory\HttpAdapter\Event\Timer\TimerInterface|\PHPUnit_Framework_MockObject_MockObject The timer mock.
     */
    private function createTimerMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Event\Timer\TimerInterface');
    }
}
