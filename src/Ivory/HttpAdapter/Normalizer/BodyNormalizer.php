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
 * Body normalizer.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class BodyNormalizer extends AbstractUninstantiableAsset
{
    /**
     * Normalizes the body.
     *
     * @param mixed  $body   The body.
     * @param string $method The method.
     *
     * @return mixed The normalized body.
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
