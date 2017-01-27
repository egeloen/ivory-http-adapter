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

use Ivory\HttpAdapter\HttpAdapterException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface PersistentCookieJarInterface extends CookieJarInterface, \Serializable
{
    /**
     * @throws HttpAdapterException
     */
    public function load();

    /**
     * @throws HttpAdapterException
     */
    public function save();
}
