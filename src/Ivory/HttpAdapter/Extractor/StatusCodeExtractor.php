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
 * Status code extractor.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatusCodeExtractor extends AbstractUninstantiableAsset
{
    /**
     * Extracts the status code.
     *
     * @param array|string $headers The headers.
     *
     * @return integer The extracted status code.
     */
    public static function extract($headers)
    {
        return (integer) substr(StatusLineExtractor::extract($headers), 9, 3);
    }
}
