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
 * Protocol version parser.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ProtocolVersionParser extends AbstractUninstantiableAsset
{
    /**
     * Parses the protocol version.
     *
     * @param array|string $headers The headers.
     *
     * @return string The parsed protocol version.
     */
    public static function parse($headers)
    {
        return substr(StatusLineParser::parse($headers), 5, 3);
    }
}
