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

use Ivory\HttpAdapter\Message\RequestInterface;
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

    public function testDefaultState()
    {
        $this->assertSame($this->httpAdapter, $this->stopwatchHttpAdapter->getHttpAdapter());
        $this->assertSame($this->stopwatch, $this->stopwatchHttpAdapter->getStopwatch());
    }

    public function testSetHttpAdapter()
    {
        $this->stopwatchHttpAdapter->setHttpAdapter($httpAdapter = $this->createHttpAdapterMock());

        $this->assertSame($httpAdapter, $this->stopwatchHttpAdapter->getHttpAdapter());
    }

    public function testSetStopwatch()
    {
        $this->stopwatchHttpAdapter->setStopwatch($stopwatch = $this->createStopwatchMock());

        $this->assertSame($stopwatch, $this->stopwatchHttpAdapter->getStopwatch());
    }

    public function testGetConfiguration()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration = $this->createConfigurationMock()));

        $this->assertSame($configuration, $this->stopwatchHttpAdapter->getConfiguration());
    }

    public function testSetConfiguration()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('setConfiguration')
            ->with($this->identicalTo($configuration = $this->createConfigurationMock()));

        $this->stopwatchHttpAdapter->setConfiguration($configuration);
    }

    public function testGetName()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->assertSame($name, $this->stopwatchHttpAdapter->getName());
    }

    /**
     * @dataProvider watchProvider
     */
    public function testWatch($method, array $params = array('url'), $wrappedMethod = 'send')
    {
        $this->stopwatch
            ->expects($this->once())
            ->method('start')
            ->with($this->identicalTo('ivory.http_adapter'));

        $this->httpAdapter
            ->expects($this->once())
            ->method($wrappedMethod)
            ->will($this->returnValue($return = 'foo'));

        $this->stopwatch
            ->expects($this->once())
            ->method('stop')
            ->with($this->identicalTo('ivory.http_adapter'));

        $this->assertSame($return, call_user_func_array(array($this->stopwatchHttpAdapter, $method), $params));
    }

    /**
     * @dataProvider watchProvider
     */
    public function testWatchException($method, array $params = array('url'), $wrappedMethod = 'send')
    {
        $this->stopwatch
            ->expects($this->once())
            ->method('start')
            ->with($this->identicalTo('ivory.http_adapter'));

        $this->httpAdapter
            ->expects($this->once())
            ->method($wrappedMethod)
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
            array('get'),
            array('head'),
            array('trace'),
            array('post'),
            array('put'),
            array('patch'),
            array('delete'),
            array('options'),
            array('send', array('url', RequestInterface::METHOD_GET)),
            array('sendRequest', array($this->getMock('Psr\Http\Message\OutgoingRequestInterface')), 'sendRequest'),
        );
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
}
