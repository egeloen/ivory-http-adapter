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
 * @author GeLo <geloen.eric@gmail.com>
 */
interface CookieFactoryInterface
{
    /**
     * @param string $name
     * @param string $value
     * @param array  $attributes
     * @param int    $createdAt
     *
     * @return CookieInterface
     */
    public function create($name, $value, array $attributes, $createdAt);

    /**
     * @param string $header
     *
     * @return CookieInterface
     */
    public function parse($header);
}
