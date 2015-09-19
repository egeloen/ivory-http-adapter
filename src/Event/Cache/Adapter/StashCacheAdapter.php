<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Cache\Adapter;

use Stash\Pool;

/**
 * Stash cache adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StashCacheAdapter implements CacheAdapterInterface
{
    /** @var \Stash\Pool */
    private $pool;

    /**
     * Creates a stash cache.
     *
     * @param \Stash\Pool $pool The stash pool.
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return !$this->pool->getItem($id)->isMiss();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->pool->getItem($id)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data, $lifeTime = 0)
    {
        $result = $this->pool->getItem($id)->set($data, $lifeTime);
        $this->pool->flush();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        $result = $this->pool->getItem($id)->clear();
        $this->pool->flush();

        return $result;
    }
}
