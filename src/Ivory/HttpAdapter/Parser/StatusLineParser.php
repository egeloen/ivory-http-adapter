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
 * Status line parser.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatusLineParser extends AbstractUninstantiableAsset
{
    /**
     * Parses the status line.
     *
     * @param array|string $headers The headers.
     *
     * @return string The parsed status line.
     */
    public static function parse($headers)
    {
        $headers = HeadersParser::parse($headers);

        return $headers[0];
    }
}
