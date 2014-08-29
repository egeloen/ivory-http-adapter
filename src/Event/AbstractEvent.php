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
use Symfony\Component\EventDispatcher\Event;

/**
 * Abstract event.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractEvent extends Event
{
    /** @var \Ivory\HttpAdapter\HttpAdapterInterface */
    protected $httpAdapter;

    /** @var \Ivory\HttpAdapter\Message\InternalRequestInterface */
    protected $request;

    /**
     * Creates a pre send event.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface             $httpAdapter The http adapter.
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request     The request.
     */
    public function __construct(HttpAdapterInterface $httpAdapter, InternalRequestInterface $request)
    {
        $this->setHttpAdapter($httpAdapter);
        $this->setRequest($request);
    }

    /**
     * Gets the http adapter.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterInterface The http adapter.
     */
    public function getHttpAdapter()
    {
        return $this->httpAdapter;
    }

    /**
     * Sets the http adapter.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface $httpAdapter The http adapter.
     */
    public function setHttpAdapter(HttpAdapterInterface $httpAdapter)
    {
        $this->httpAdapter = $httpAdapter;
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
