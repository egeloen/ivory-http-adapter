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

use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractEventTest extends AbstractTestCase
{
    /**
     * @var mixed
     */
    protected $event;

    /**
     * @var HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $httpAdapter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->httpAdapter = $this->createHttpAdapterMock();
        $this->event = $this->createEvent();
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Event\AbstractEvent', $this->event);
        $this->assertSame($this->httpAdapter, $this->event->getHttpAdapter());
    }

    /**
     * @return mixed
     */
    abstract protected function createEvent();

    /**
     * @return HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createHttpAdapterMock()
    {
        return $this->createMock('Ivory\HttpAdapter\HttpAdapterInterface');
    }
}
