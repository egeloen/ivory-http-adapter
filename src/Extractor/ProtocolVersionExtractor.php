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
 * @author GeLo <geloen.eric@gmail.com>
 */
class ProtocolVersionExtractor extends AbstractUninstantiableAsset
{
    /**
     * @param array|string $headers
     *
     * @return string
     */
    public static function extract($headers)
    {
        return substr(StatusLineExtractor::extract($headers), 5, 3);
    }
}
