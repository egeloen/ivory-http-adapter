<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Normalizer;

use Ivory\HttpAdapter\Asset\AbstractUninstantiableAsset;
use Ivory\HttpAdapter\Parser\HeadersParser;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class HeadersNormalizer extends AbstractUninstantiableAsset
{
    /**
     * @param string|array $headers
     * @param bool         $associative
     *
     * @return array
     */
    public static function normalize($headers, $associative = true)
    {
        $normalizedHeaders = [];

        if (!$associative) {
            $headers = self::normalize($headers);
        }

        foreach (HeadersParser::parse($headers) as $name => $value) {
            if (strpos($value, 'HTTP/') === 0) {
                continue;
            }

            list($name, $value) = explode(':', $value, 2);

            $name = self::normalizeHeaderName($name);
            $value = self::normalizeHeaderValue($value);

            if (!$associative) {
                $normalizedHeaders[] = $name.': '.$value;
            } else {
                $normalizedHeaders[$name] = isset($normalizedHeaders[$name])
                    ? $normalizedHeaders[$name].', '.$value
                    : $value;
            }
        }

        return $normalizedHeaders;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public static function normalizeHeaderName($name)
    {
        return trim($name);
    }

    /**
     * @param array|string $value
     *
     * @return string
     */
    public static function normalizeHeaderValue($value)
    {
        return implode(', ', array_map('trim', is_array($value) ? $value : explode(',', $value)));
    }
}
