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
use Ivory\HttpAdapter\Message\RequestInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class BodyNormalizer extends AbstractUninstantiableAsset
{
    /**
     * @param mixed  $body
     * @param string $method
     *
     * @return mixed
     */
    public static function normalize($body, $method)
    {
        if ($method === RequestInterface::METHOD_HEAD || empty($body)) {
            return;
        }

        if (is_callable($body)) {
            return call_user_func($body);
        }

        return $body;
    }
}
