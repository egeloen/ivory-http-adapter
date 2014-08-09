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
use Ivory\HttpAdapter\Normalizer\HeadersNormalizer;

/**
 * Effective url parser.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class EffectiveUrlParser extends AbstractUninstantiableAsset
{
    /**
     * Parses the effective url.
     *
     * @param array|string $headers         The headers.
     * @param string       $url             The url.
     * @param boolean      $hasMaxRedirects TRUE if there are max redirects else FALSE.
     *
     * @return string The parsed effective url.
     */
    public static function parse($headers, $url, $hasMaxRedirects)
    {
        if (is_array($headers)) {
            $normalizedHeaders = array();

            foreach ($headers as $name => $value) {
                $value = HeadersNormalizer::normalizeHeaderValue($value);
                $normalizedHeaders[] = is_int($name) ? $value : $name.': '.$value;
            }

            $headers = implode("\r\n", $normalizedHeaders);
        }

        if ($hasMaxRedirects && preg_match_all('/(L|l)ocation:([^(\\r\\n)]+)/', $headers, $matches)) {
            return trim($matches[2][count($matches[2]) - 1]);
        }

        return $url;
    }
}
