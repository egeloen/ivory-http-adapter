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
 * @author GeLo <geloen.eric@gmail.com>
 */
class CookieParser extends AbstractUninstantiableAsset
{
    /**
     * @param string $header
     *
     * @return array
     */
    public static function parse($header)
    {
        if (strpos($header, '=') === false) {
            $header = '='.$header;
        }

        list($name, $header) = explode('=', $header, 2);

        if (strpos($header, ';') === false) {
            $value = $header;
            $header = null;
        } else {
            list($value, $header) = explode(';', $header, 2);
        }

        $attributes = [];
        foreach (array_map('trim', explode(';', $header)) as $pair) {
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

        $name = trim($name);
        $value = trim($value);

        return [!empty($name) ? $name : null, !empty($value) ? $value : null, $attributes];
    }
}
