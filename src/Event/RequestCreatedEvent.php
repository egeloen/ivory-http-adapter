<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Event;

use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * Request created event.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RequestCreatedEvent extends AbstractEvent
{
    /** @var \Ivory\HttpAdapter\Message\InternalRequestInterface */
    private $request;

    /**
     * Creates a request created event.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface             $httpAdapter The http adapter.
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request     The request.
     */
    public function __construct(HttpAdapterInterface $httpAdapter, InternalRequestInterface $request)
    {
        parent::__construct($httpAdapter);

        $this->setRequest($request);
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
     * Sets the request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     */
    public function setRequest(InternalRequestInterface $request)
    {
        $this->request = $request;
    }
}
