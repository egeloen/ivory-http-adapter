<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Cookie\Jar;

/**
 * Persistent cookie jar.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface PersistentCookieJarInterface extends CookieJarInterface, \Serializable
{
    /**
     * Loads the cookie jar.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     */
    public function load();

    /**
     * Saves the cookie jar.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     */
    public function save();
}
