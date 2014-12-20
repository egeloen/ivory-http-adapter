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

/**
 * Timer test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TimerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Event\Timer\Timer */
    private $timer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->timer = new Timer();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->timer);
    }

    public function testStart()
    {
        $start = null;

        $request = $this->createRequestMock();
        $request
            ->expects($this->once())
            ->method('removeParameter')
            ->with($this->identicalTo(Timer::TIME));

        $request
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                $this->identicalTo(Timer::START_TIME),
                $this->callback(function ($parameter) use (&$start) {
                    $start = $parameter;

                    return true;
                })
            );

        $before = microtime(true);
        $this->timer->start($request);
        $after = microtime(true);

        $this->assertGreaterThanOrEqual($before, $start);
        $this->assertLessThanOrEqual($after, $start);
    }

    public function testStop()
    {
        $time = null;
        $before = microtime(true);

        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('hasParameter')
            ->will($this->returnValueMap(array(
                array(Timer::START_TIME, true),
                array(Timer::TIME, false),
            )));

        $request
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(Timer::START_TIME))
            ->will($this->returnValue($start = microtime(true)));

        $request
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                $this->identicalTo(Timer::TIME),
                $this->callback(function ($parameter) use (&$time) {
                    $time = $parameter;

                    return true;
                })
            );

        $this->timer->stop($request);
        $after = microtime(true);

        $this->assertGreaterThanOrEqual($before - $start, $time);
        $this->assertLessThanOrEqual($after - $start, $time);
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
}
