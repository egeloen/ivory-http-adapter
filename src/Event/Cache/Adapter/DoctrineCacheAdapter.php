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

use Doctrine\Common\Cache\Cache;

/**
 * Doctrine cache adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class DoctrineCacheAdapter implements CacheAdapterInterface
{
    /** @var \Doctrine\Common\Cache\Cache */
    private $cache;

    /**
     * Creates a doctrine cache.
     *
     * @param \Doctrine\Common\Cache\Cache $cache The doctrine cache.
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return $this->cache->contains($id);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->has($id) ? $this->cache->fetch($id) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data, $lifeTime = 0)
    {
        return $this->cache->save($id, $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        return $this->cache->delete($id);
    }
}
