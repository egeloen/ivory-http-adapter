<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Timer;

use Ivory\HttpAdapter\Event\Timer\Timer;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TimerTest extends AbstractTestCase
{
    /**
     * @var Timer
     */
    private $timer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->timer = new Timer();
    }

    public function testStart()
    {
        $start = null;

        $request = $this->createRequestMock();
        $request
            ->expects($this->once())
            ->method('withoutParameter')
            ->with($this->identicalTo(Timer::TIME))
            ->will($this->returnValue($request));

        $request
            ->expects($this->once())
            ->method('withParameter')
            ->with(
                $this->identicalTo(Timer::START_TIME),
                $this->callback(function ($parameter) use (&$start) {
                    $start = $parameter;

                    return true;
                })
            )
            ->will($this->returnValue($request));

        $before = microtime(true);
        $result = $this->timer->start($request);
        $after = microtime(true);

        $this->assertGreaterThanOrEqual($before, $start);
        $this->assertLessThanOrEqual($after, $start);
        $this->assertSame($request, $result);
    }

    public function testStop()
    {
        $time = null;
        $before = microtime(true);

        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('hasParameter')
            ->will($this->returnValueMap([
                [Timer::START_TIME, true],
                [Timer::TIME, false],
            ]));

        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(Timer::START_TIME))
            ->will($this->returnValue($start = microtime(true)));

        $request
            ->expects($this->once())
            ->method('withParameter')
            ->with(
                $this->identicalTo(Timer::TIME),
                $this->callback(function ($parameter) use (&$time) {
                    $time = $parameter;

                    return true;
                })
            )
            ->will($this->returnValue($request));

        $result = $this->timer->stop($request);
        $after = microtime(true);

        $this->assertGreaterThanOrEqual($before - $start, $time);
        $this->assertLessThanOrEqual($after - $start, $time);
        $this->assertSame($request, $result);
    }

    public function testStopAgain()
    {
        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('hasParameter')
            ->will($this->returnValueMap([
                [Timer::START_TIME, true],
                [Timer::TIME, true],
            ]));

        $request
            ->expects($this->never())
            ->method('withParameter');

        $result = $this->timer->stop($request);

        $this->assertSame($request, $result);
    }

    /**
     * @return InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRequestMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }
}
