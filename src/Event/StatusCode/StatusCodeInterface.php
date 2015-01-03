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
interface StatusCodeInterface
{
    /**
     * Validates a status code.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface $response The response.
     *
     * @return boolean TRUE if the status code is valid else FALSE.
     */
    public function validate(ResponseInterface $response);
}
