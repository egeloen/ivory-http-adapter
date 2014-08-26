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
 * Request interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface RequestInterface extends PsrRequestInterface, MessageInterface
{
    /** @const string The GET method. */
    const METHOD_GET = 'GET';

    /** @const string The HEAD method. */
    const METHOD_HEAD = 'HEAD';

    /** @const string The TRACE method. */
    const METHOD_TRACE = 'TRACE';

    /** @const string The POST method. */
    const METHOD_POST = 'POST';

    /** @const string The PUT method. */
    const METHOD_PUT = 'PUT';

    /** @const string The PATCH method. */
    const METHOD_PATCH = 'PATCH';

    /** @const string The DELETE method. */
    const METHOD_DELETE = 'DELETE';

    /** @const string The OPTIONS method. */
    const METHOD_OPTIONS = 'OPTIONS';
}
