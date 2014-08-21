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

/**
 * Protocol version extractor.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ProtocolVersionExtractor extends AbstractUninstantiableAsset
{
    /**
     * Extracts the protocol version.
     *
     * @param array|string $headers The headers.
     *
     * @return string The extracted protocol version.
     */
    public static function extract($headers)
    {
        return substr(StatusLineExtractor::extract($headers), 5, 3);
    }
}
