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
 * Cache adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface CacheAdapterInterface
{
    /**
     * Checks if an entry exists.
     *
     * @param string $id The identifier.
     *
     * @return boolean TRUE if a entry exists else FALSE.
     */
    public function has($id);

    /**
     * Gets an entry.
     *
     * @param string $id The identifier.
     *
     * @return mixed The data.
     */
    public function get($id);

    /**
     * Sets an entry.
     *
     * @param string $id       The identifier.
     * @param mixed  $data     The data.
     * @param int    $lifeTime The lifetime.
     *
     * @return boolean TRUE if the entry was saved else FALSE.
     */
    public function set($id, $data, $lifeTime = 0);

    /**
     * Removes an entry.
     *
     * @param string $id The identifier.
     *
     * @return boolean TRUE if the entry was deleted else FALSE.
     */
    public function remove($id);
}
