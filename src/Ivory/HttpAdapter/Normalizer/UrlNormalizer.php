<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Normalizer;

use Ivory\HttpAdapter\Asset\AbstractUninstantiableAsset;

/**
 * Url normalizer.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class UrlNormalizer extends AbstractUninstantiableAsset
{
    /**
     * Normalizes an url.
     *
     * @param string|object $url The url.
     *
     * @return string The normalized url.
     */
    public static function normalize($url)
    {
        return strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0 ? 'http://'.$url : (string) $url;
    }
}
