<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Extractor;

use Ivory\HttpAdapter\Asset\AbstractUninstantiableAsset;
use Ivory\HttpAdapter\Parser\HeadersParser;

/**
 * Status line extractor.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatusLineExtractor extends AbstractUninstantiableAsset
{
    /**
     * Extracts the status line.
     *
     * @param array|string $headers The headers.
     *
     * @return string The extracted status line.
     */
    public static function extract($headers)
    {
        $headers = HeadersParser::parse($headers);

        return $headers[0];
    }
}
