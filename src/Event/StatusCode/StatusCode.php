<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\StatusCode;

use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * Status code.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StatusCode implements StatusCodeInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate(ResponseInterface $response)
    {
        $statusCode = (string) $response->getStatusCode();

        return $statusCode[0] !== '4' && $statusCode[0] !== '5';
    }
}
