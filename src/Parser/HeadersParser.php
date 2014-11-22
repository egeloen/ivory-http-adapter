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
 * Headers parser.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HeadersParser extends AbstractUninstantiableAsset
{
    /**
     * Parses the headers.
     *
     * @param array|string $headers The headers.
     *
     * @return array The parsed headers.
     */
    public static function parse($headers)
    {
        if (is_string($headers)) {
            $headers = explode("\r\n\r\n", trim($headers));

            return explode("\r\n", end($headers));
        }

        $parsedHeaders = array();

        foreach ($headers as $name => $value) {
            $value = HeadersNormalizer::normalizeHeaderValue($value);

            if (is_int($name)) {
                if (strpos($value, 'HTTP/') === 0) {
                    $parsedHeaders = array($value);
                } else {
                    $parsedHeaders[] = $value;
                }
            } else {
                $parsedHeaders[] = $name.': '.$value;
            }
        }

        return $parsedHeaders;
    }
}
