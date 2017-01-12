<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Cache\Adapter;

use Ivory\HttpAdapter\Event\Cache\Adapter\StashCacheAdapter;
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * Stash cache adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StashCacheAdapterTest extends AbstractTestCase
{
    /** @var \Ivory\HttpAdapter\Event\Cache\Adapter\StashCacheAdapter */
    private $stashCacheAdapter;

    /** @var \Stash\Pool|\PHPUnit_Framework_MockObject_MockObject */
    private $pool;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->pool = $this->createPoolMock();
        $this->stashCacheAdapter = new StashCacheAdapter($this->pool);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->pool);
        unset($this->stashCacheAdapter);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf(
            'Ivory\HttpAdapter\Event\Cache\Adapter\CacheAdapterInterface',
            $this->stashCacheAdapter
        );
    }

    public function testHas()
    {
        $this->pool
            ->expects($this->once())
            ->method('getItem')
            ->with($this->identicalTo($id = 'id'))
            ->will($this->returnValue($item = $this->createItemMock()));

        $item
            ->expects($this->once())
            ->method('isMiss')
            ->will($this->returnValue(false));

        $this->assertTrue($this->stashCacheAdapter->has($id));
    }

    public function testGet()
    {
        $this->pool
            ->expects($this->once())
            ->method('getItem')
            ->with($this->identicalTo($id = 'id'))
            ->will($this->returnValue($item = $this->createItemMock()));

        $item
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($data = 'data'));

        $this->assertSame($data, $this->stashCacheAdapter->get($id));
    }

    public function testSet()
    {
        $this->pool
            ->expects($this->once())
            ->method('getItem')
            ->with($this->identicalTo($id = 'id'))
            ->will($this->returnValue($item = $this->createItemMock()));

        $item
            ->expects($this->once())
            ->method('set')
            ->with($this->identicalTo($data = 'data'), $this->identicalTo($lifeTime = 123))
            ->will($this->returnValue(true));

        $this->pool
            ->expects($this->once())
            ->method('flush');

        $this->assertTrue($this->stashCacheAdapter->set($id, $data, $lifeTime));
    }

    public function testRemove()
    {
        $this->pool
            ->expects($this->once())
            ->method('getItem')
            ->with($this->identicalTo($id = 'id'))
            ->will($this->returnValue($item = $this->createItemMock()));

        $item
            ->expects($this->once())
            ->method('clear')
            ->will($this->returnValue(true));

        $this->pool
            ->expects($this->once())
            ->method('flush');

        $this->assertTrue($this->stashCacheAdapter->remove($id));
    }

    /**
     * Creates a pool mock.
     *
     * @return \Stash\Pool|\PHPUnit_Framework_MockObject_MockObject The pool mock.
     */
    private function createPoolMock()
    {
        return $this->getMockBuilder('Stash\Pool')
            ->setMethods(array('getItem', 'flush'))
            ->getMock();
    }

    /**
     * @return \Stash\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createItemMock()
    {
        return $this->createMock('Stash\Item');
    }
}
