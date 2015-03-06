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

use Ivory\HttpAdapter\StopwatchHttpAdapter;

/**
 * Stopwatch http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StopwatchHttpAdapterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\StopwatchHttpAdapter */
    private $stopwatchHttpAdapter;

    /** @var \Ivory\HttpAdapter\HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $httpAdapter;

    /** @var \Symfony\Component\Stopwatch\Stopwatch|\PHPUnit_Framework_MockObject_MockObject */
    private $stopwatch;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->stopwatchHttpAdapter = new StopwatchHttpAdapter(
            $this->httpAdapter = $this->createHttpAdapterMock(),
            $this->stopwatch = $this->createStopwatchMock()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->stopwatch);
        unset($this->httpAdapter);
        unset($this->stopwatchHttpAdapter);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\PsrHttpAdapterDecorator', $this->stopwatchHttpAdapter);
    }

    /**
     * @dataProvider watchProvider
     */
    public function testWatch($method, array $params, $result)
    {
        $this->stopwatch
            ->expects($this->once())
            ->method('start')
            ->with($this->identicalTo('ivory.http_adapter'));

        $this->httpAdapter
            ->expects($this->once())
            ->method($method)
            ->will($this->returnValue($result));

        $this->stopwatch
            ->expects($this->once())
            ->method('stop')
            ->with($this->identicalTo('ivory.http_adapter'));

        $this->assertSame($result, call_user_func_array(array($this->stopwatchHttpAdapter, $method), $params));
    }

    /**
     * @dataProvider watchProvider
     */
    public function testWatchException($method, array $params)
    {
        $this->stopwatch
            ->expects($this->once())
            ->method('start')
            ->with($this->identicalTo('ivory.http_adapter'));

        $this->httpAdapter
            ->expects($this->once())
            ->method($method)
            ->will($this->throwException($exception = new \Exception()));

        $this->stopwatch
            ->expects($this->once())
            ->method('stop')
            ->with($this->identicalTo('ivory.http_adapter'));

        try {
            call_user_func_array(array($this->stopwatchHttpAdapter, $method), $params);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertSame($exception, $e);
        }
    }

    /**
     * Gets the watch provider.
     *
     * @return array The watch provider.
     */
    public function watchProvider()
    {
        return array(
            array('sendRequest', array($this->createInternalRequestMock()), $this->createResponseMock()),
            array('sendRequests', array(array($this->createInternalRequestMock())), array($this->createResponseMock())),
        );
    }

    /**
     * Creates an http adapter mock.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject The http adapter mock.
     */
    private function createHttpAdapterMock()
    {
        return $this->getMock('Ivory\HttpAdapter\HttpAdapterInterface');
    }

    /**
     * Creates a stopwatch mock.
     *
     * @return \Symfony\Component\Stopwatch\Stopwatch|\PHPUnit_Framework_MockObject_MockObject The stopwatch mock.
     */
    private function createStopwatchMock()
    {
        return $this->getMock('Symfony\Component\Stopwatch\Stopwatch');
    }

    /**
     * Creates a request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The request mock.
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
}
