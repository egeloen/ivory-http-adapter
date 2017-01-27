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
class StatusCodeExtractor extends AbstractUninstantiableAsset
{
    /**
     * @param array|string $headers
     *
     * @return int
     */
    public static function extract($headers)
    {
        return (int) substr(StatusLineExtractor::extract($headers), 9, 3);
    }
}
