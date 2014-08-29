<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\Cookie;

/**
 * Cookie factory.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface CookieFactoryInterface
{
    /**
     * Creates a cookie.
     *
     * @param string  $name       The name.
     * @param string  $value      The value.
     * @param array   $attributes The attributes.
     * @param integer $createdAt  The creation date (unix timestamp).
     *
     * @return \Ivory\HttpAdapter\Event\Cookie\CookieInterface The cookie.
     */
    public function create($name, $value, array $attributes, $createdAt);

    /**
     * Parses a cookie.
     *
     * @param string $header The header.
     *
     * @return \Ivory\HttpAdapter\Event\Cookie\CookieInterface The parsed cookie.
     */
    public function parse($header);
}
