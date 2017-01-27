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

use Psr\Http\Message\RequestInterface as PsrRequestInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface RequestInterface extends PsrRequestInterface, MessageInterface
{
    const METHOD_GET = 'GET';
    const METHOD_HEAD = 'HEAD';
    const METHOD_TRACE = 'TRACE';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';
}
