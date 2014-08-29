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
 * Headers normalizer.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HeadersNormalizer extends AbstractUninstantiableAsset
{
    /**
     * Normalizes the headers.
     *
     * @param string|array $headers     The headers.
     * @param boolean      $associative TRUE if the headers should be an associative array else FALSE.
     *
     * @return array The normalized headers.
     */
    public static function normalize($headers, $associative = true)
    {
        $normalizedHeaders = array();

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
     * Normalizes the header name.
     *
     * @param string $name The header name.
     *
     * @return string The normalized header name.
     */
    public static function normalizeHeaderName($name)
    {
        return trim($name);
    }

    /**
     * Normalizes the header value.
     *
     * @param array|string $value The header value.
     *
     * @return string The normalized header value.
     */
    public static function normalizeHeaderValue($value)
    {
        return implode(', ', array_map('trim', is_array($value) ? $value : explode(',', $value)));
    }
}
