<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event\History;

use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;

/**
 * Journal entry.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface JournalEntryInterface
{
    /**
     * Gets the request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The request.
     */
    public function getRequest();

    /**
     * Sets the request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     */
    public function setRequest(InternalRequestInterface $request);

    /**
     * Gets the response.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function getResponse();

    /**
     * Sets the response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface $response The response.
     */
    public function setResponse(ResponseInterface $response);
}
