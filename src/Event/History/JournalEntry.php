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
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class JournalEntry implements JournalEntryInterface
{
    /** @var \Ivory\HttpAdapter\Message\InternalRequestInterface */
    private $request;

    /** @var \Ivory\HttpAdapter\Message\ResponseInterface */
    private $response;

    /**
     * Creates a journal entry.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request  The request.
     * @param \Ivory\HttpAdapter\Message\ResponseInterface        $response The response.
     */
    public function __construct(InternalRequestInterface $request, ResponseInterface $response)
    {
        $this->setRequest($request);
        $this->setResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequest(InternalRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * {@inheritdoc}
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }
}
