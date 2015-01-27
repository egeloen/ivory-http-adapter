<?php

/**
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\TapeRecorder;

use Ivory\HttpAdapter\HttpAdapterException;

/**
 * TapeRecorder Exception
 *
 * A special type of exception which can be used to intercept a request and set the request's
 * response and exception with recorded values
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
class TapeRecorderException extends HttpAdapterException
{
    /**
     * @return TapeRecorderException
     */
    public static function interceptingRequest()
    {
        return new static("Intercepting request.");
    }
}
