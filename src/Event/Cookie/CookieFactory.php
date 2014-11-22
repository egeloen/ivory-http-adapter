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

use Ivory\HttpAdapter\Parser\CookieParser;

/**
 * Cookie factory.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CookieFactory implements CookieFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create($name, $value, array $attributes, $createdAt)
    {
        return new Cookie($name, $value, $attributes, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($header)
    {
        list($name, $value, $attributes) = CookieParser::parse($header);

        return $this->create($name, $value, $attributes, time());
    }
}
