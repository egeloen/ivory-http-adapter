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
class JournalEntry
{
    /** @var \Ivory\HttpAdapter\Message\InternalRequestInterface */
    protected $request;

    /** @var \Ivory\HttpAdapter\Message\ResponseInterface */
    protected $response;

    /** @var float */
    protected $time;

    /**
     * Creates a journal entry.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request  The request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response The response.
     * @param float                                               $time     The time.
     */
    public function __construct(InternalRequestInterface $request, ResponseInterface $response, $time)
    {
        $this->request = $request;
        $this->response = $response;
        $this->time = $time;
    }

    /**
     * Gets the request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface The request.
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Gets the response.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Gets the time.
     *
     * @return float The time.
     */
    public function getTime()
    {
        return $this->time;
    }
}
