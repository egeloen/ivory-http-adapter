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

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface CacheAdapterInterface
{
    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id);

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function get($id);

    /**
     * @param string $id
     * @param mixed  $data
     * @param int    $lifeTime
     *
     * @return bool
     */
    public function set($id, $data, $lifeTime = 0);

    /**
     * @param string $id
     *
     * @return bool
     */
    public function remove($id);
}
