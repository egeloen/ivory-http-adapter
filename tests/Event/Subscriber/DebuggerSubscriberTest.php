<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Subscriber;

/**
 * Debugger subscriber test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class DebuggerSubscriberTest extends AbstractSubscriberTest
{
    /** @var \Ivory\HttpAdapter\Event\Subscriber\AbstractDebuggerSubscriber */
    private $debuggerSubscriber;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->debuggerSubscriber = $this->getMockForAbstractClass(
            'Ivory\HttpAdapter\Event\Subscriber\AbstractDebuggerSubscriber'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->debuggerSubscriber);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Subscriber\AbstractTimerSubscriber',
            $this->debuggerSubscriber
        );
    }

    public function testPostSendEvent()
    {
        $httpAdapter = $this->createHttpAdapterMock();
        $request = $this->createRequestMock();
        $response = $this->createResponseMock();

        $this->debuggerSubscriber->onPreSend($this->createPreSendEvent($httpAdapter, $request));
        $datas = $this->debuggerSubscriber->onPostSend($this->createPostSendEvent($httpAdapter, $request, $response));

        $this->assertCount(4, $datas);

        $this->assertArrayHasKey('time', $datas);
        $this->assertGreaterThan(0, $datas['time']);
        $this->assertLessThan(1, $datas['time']);

        $this->assertArrayHasKey('adapter', $datas);
        $this->assertSame($httpAdapter->getName(), $datas['adapter']);

        $this->assertArrayHasKey('request', $datas);
        $this->assertSame(
            array(
                'protocol_version' => $request->getProtocolVersion(),
                'url'              => $request->getUrl(),
                'method'           => $request->getMethod(),
                'headers'          => $request->getHeaders(),
                'raw_datas'        => $request->getRawDatas(),
                'datas'            => $request->getDatas(),
                'files'            => $request->getFiles(),
                'parameters'       => $request->getParameters(),
            ),
            $datas['request']
        );

        $this->assertArrayHasKey('response', $datas);
        $this->assertSame(
            array(
                'protocol_version' => $response->getProtocolVersion(),
                'status_code'      => $response->getStatusCode(),
                'reason_phrase'    => $response->getReasonPhrase(),
                'headers'          => $response->getHeaders(),
                'body'             => (string) $response->getBody(),
                'parameters'       => $response->getParameters(),
            ),
            $datas['response']
        );
    }

    public function testExceptionEvent()
    {
        $httpAdapter = $this->createHttpAdapterMock();
        $request = $this->createRequestMock();
        $exception = $this->createExceptionMock();

        $this->debuggerSubscriber->onPreSend($this->createPreSendEvent($httpAdapter, $request));
        $datas = $this->debuggerSubscriber->onException(
            $this->createExceptionEvent($httpAdapter, $request, $exception)
        );

        $this->assertCount(4, $datas);

        $this->assertArrayHasKey('time', $datas);
        $this->assertGreaterThan(0, $datas['time']);
        $this->assertLessThan(1, $datas['time']);

        $this->assertArrayHasKey('adapter', $datas);
        $this->assertSame($httpAdapter->getName(), $datas['adapter']);

        $this->assertArrayHasKey('request', $datas);
        $this->assertSame(
            array(
                'protocol_version' => $request->getProtocolVersion(),
                'url'              => $request->getUrl(),
                'method'           => $request->getMethod(),
                'headers'          => $request->getHeaders(),
                'raw_datas'        => $request->getRawDatas(),
                'datas'            => $request->getDatas(),
                'files'            => $request->getFiles(),
                'parameters'       => $request->getParameters(),
            ),
            $datas['request']
        );

        $this->assertArrayHasKey('exception', $datas);
        $this->assertSame(
            array(
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
                'line'    => $exception->getLine(),
                'file'    => $exception->getFile(),
            ),
            $datas['exception']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapterMock()
    {
        $httpAdapter = parent::createHttpAdapterMock();
        $httpAdapter
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('name'));

        return $httpAdapter;
    }

    /**
     * {@inheritdoc}
     */
    protected function createRequestMock()
    {
        $request = parent::createRequestMock();
        $request
            ->expects($this->any())
            ->method('getProtocolVersion')
            ->will($this->returnValue('1.1'));

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
            ->method('getHeaders')
            ->will($this->returnValue(array('foo' => 'bar')));

        $request
            ->expects($this->any())
            ->method('getRawDatas')
            ->will($this->returnValue('foo=bar'));

        $request
            ->expects($this->any())
            ->method('getDatas')
            ->will($this->returnValue(array('baz' => 'bat')));

        $request
            ->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue(array('bit' => __FILE__)));

        $request
            ->expects($this->any())
            ->method('getParameters')
            ->will($this->returnValue(array('ban' => 'bor')));

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    protected function createResponseMock()
    {
        $response = parent::createResponseMock();
        $response
            ->expects($this->any())
            ->method('getProtocolVersion')
            ->will($this->returnValue('1.1'));

        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(200));

        $response
            ->expects($this->any())
            ->method('getReasonPhrase')
            ->will($this->returnValue('OK'));

        $response
            ->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue(array('bal' => 'bol')));

        $response
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue('body'));

        $response
            ->expects($this->any())
            ->method('getParameters')
            ->will($this->returnValue(array('bil' => 'bob')));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    protected function createExceptionMock()
    {
        $exception = parent::createExceptionMock();
        $exception
            ->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue(123));

        $exception
            ->expects($this->any())
            ->method('getMessage')
            ->will($this->returnValue('message'));

        $exception
            ->expects($this->any())
            ->method('getLine')
            ->will($this->returnValue(234));

        $exception
            ->expects($this->any())
            ->method('getFile')
            ->will($this->returnValue(__FILE__));

        return $exception;
    }
}
