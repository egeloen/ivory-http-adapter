<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Parser;

use Ivory\HttpAdapter\Asset\AbstractUninstantiableAsset;

/**
 * Cookie parser.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CookieParser extends AbstractUninstantiableAsset
{
    /**
     * Parses a cookie header.
     *
     * @param string $header The cookie header.
     *
     * @return array The parsed cookie header (0 => name, 1 => value, 2 => attributes).
     */
    public static function parse($header)
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

        return array(trim($name), trim($value), $attributes);
    }
}
