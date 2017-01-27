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

use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\HttpAdapter\StopwatchHttpAdapter;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class StopwatchHttpAdapterTest extends AbstractTestCase
{
    /**
     * @var StopwatchHttpAdapter
     */
    private $stopwatchHttpAdapter;

    /**
     * @var HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpAdapter;

    /**
     * @var Stopwatch|\PHPUnit_Framework_MockObject_MockObject
     */
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

    public function testInheritance()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\PsrHttpAdapterDecorator', $this->stopwatchHttpAdapter);
    }

    /**
     * @param string            $method
     * @param array             $params
     * @param ResponseInterface $result
     *
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

        $this->assertSame($result, call_user_func_array([$this->stopwatchHttpAdapter, $method], $params));
    }

    /**
     * @param string $method
     * @param array  $params
     *
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
            call_user_func_array([$this->stopwatchHttpAdapter, $method], $params);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertSame($exception, $e);
        }
    }

    /**
     * @return array
     */
    public function watchProvider()
    {
        return [
            ['sendRequest', [$this->createInternalRequestMock()], $this->createResponseMock()],
            ['sendRequests', [[$this->createInternalRequestMock()]], [$this->createResponseMock()]],
        ];
    }

    /**
     * @return HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createHttpAdapterMock()
    {
        return $this->createMock('Ivory\HttpAdapter\HttpAdapterInterface');
    }

    /**
     * @return Stopwatch|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createStopwatchMock()
    {
        return $this->createMock('Symfony\Component\Stopwatch\Stopwatch');
    }

    /**
     * @return InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createInternalRequestMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * @return ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createResponseMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }
}
