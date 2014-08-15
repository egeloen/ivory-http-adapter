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

use Ivory\HttpAdapter\Event\Cookie\Cookie;

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
        list($name, $header) = explode('=', $header, 2);

        if (strpos($header, ';') === false) {
            $value = $header;
            $header = null;
        } else {
            list($value, $header) = explode(';', $header, 2);
        }

        $attributes = array();
        foreach (explode(';', $header) as $pair) {
            if (empty($pair)) {
                continue;
            }

            if (strpos($pair, '=') === false) {
                $attributeName = $pair;
                $attributeValue = null;
            } else {
                list($attributeName, $attributeValue) = explode('=', $pair);
            }

            $attributes[trim($attributeName)] = $attributeValue ? trim($attributeValue) : true;
        }

        return $this->create(trim($name), trim($value), $attributes, time());
    }
}
