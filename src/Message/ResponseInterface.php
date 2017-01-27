<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Message;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ResponseInterface extends PsrResponseInterface, MessageInterface
{
}
