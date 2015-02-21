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
use Ivory\HttpAdapter\Message\RequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * Track
 *
 * @author Jérôme Gamez <jerome@gamez.name>
 */
interface TrackInterface
{
    /**
     * Gets the request.
     *
     * @return RequestInterface The request.
     */
    public function getRequest();

    /**
     * Whether the track has a response or not.
     *
     * @return bool
     */
    public function hasResponse();

    /**
     * Gets the response.
     *
     * @return ResponseInterface The response.
     */
    public function getResponse();

    /**
     * Sets the response.
     *
     * @param ResponseInterface $response The response.
     *
     * @return void No return value.
     */
    public function setResponse(ResponseInterface $response = null);

    /**
     * Whether the track has an exception or not.
     *
     * @return bool
     */
    public function hasException();

    /**
     * Gets the exception.
     *
     * @return HttpAdapterException The exception.
     */
    public function getException();

    /**
     * Sets the exception.
     *
     * @param HttpAdapterException|null $exception The exception.
     *
     * @return void No return value.
     */
    public function setException(HttpAdapterException $exception = null);
}
