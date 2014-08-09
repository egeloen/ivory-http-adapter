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
 * Reason phrase parser.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ReasonPhraseParser extends AbstractUninstantiableAsset
{
    /**
     * Parses the reason phrase.
     *
     * @param array|string $headers The headers.
     *
     * @return string The parsed reason phrase.
     */
    public static function parse($headers)
    {
        return substr(StatusLineParser::parse($headers), 13);
    }
}
