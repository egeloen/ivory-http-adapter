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
 * Status code parser.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatusCodeParser extends AbstractUninstantiableAsset
{
    /**
     * Parses the status code.
     *
     * @param array|string $headers The headers.
     *
     * @return integer The parsed status code.
     */
    public static function parse($headers)
    {
        return (integer) substr(StatusLineParser::parse($headers), 9, 3);
    }
}
