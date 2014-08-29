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
 * Method normalizer.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MethodNormalizer extends AbstractUninstantiableAsset
{
    /**
     * Normalizes a method.
     *
     * @param string $method The method.
     *
     * @return string The normalized method.
     */
    public static function normalize($method)
    {
        return strtoupper($method);
    }
}
