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
use Ivory\HttpAdapter\HttpAdapterException;

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
     * @throws HttpAdapterException If the url is not valid.
     *
     * @return string The normalized url.
     */
    public static function normalize($url)
    {
        $url = (string) $url;

        if ((($parts = parse_url($url)) === false) || !isset($parts['scheme']) || !isset($parts['host'])) {
            throw HttpAdapterException::urlIsNotValid($url);
        }

        return $url;
    }
}
