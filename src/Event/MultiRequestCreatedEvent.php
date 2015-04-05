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
 * Multi request created event.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class MultiRequestCreatedEvent extends AbstractEvent
{
    /** @var array */
    private $requests;

    /**
     * Creates a multi request created event.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface $httpAdapter The http adapter.
     * @param array                                   $requests    The requests.
     */
    public function __construct(HttpAdapterInterface $httpAdapter, array $requests)
    {
        parent::__construct($httpAdapter);

        $this->setRequests($requests);
    }

    /**
     * Clears the requests.
     */
    public function clearRequests()
    {
        $this->requests = array();
    }

    /**
     * Checks if there are requests.
     *
     * @return boolean TRUE if there are requests else FALSE.
     */
    public function hasRequests()
    {
        return !empty($this->requests);
    }

    /**
     * Gets the requests.
     *
     * @return array The requests.
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * Sets the requests.
     *
     * @param array $requests The requests.
     */
    public function setRequests(array $requests)
    {
        $this->clearRequests();
        $this->addRequests($requests);
    }

    /**
     * Adds the requests.
     *
     * @param array $requests The requests.
     */
    public function addRequests(array $requests)
    {
        foreach ($requests as $request) {
            $this->addRequest($request);
        }
    }

    /**
     * Removes the requests.
     *
     * @param array $requests The requests.
     */
    public function removeRequests(array $requests)
    {
        foreach ($requests as $request) {
            $this->removeRequest($request);
        }
    }

    /**
     * Checks if there is a request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     *
     * @return boolean TRUE if there is the request else FALSE.
     */
    public function hasRequest(InternalRequestInterface $request)
    {
        return array_search($request, $this->requests, true) !== false;
    }

    /**
     * Adds a request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     */
    public function addRequest(InternalRequestInterface $request)
    {
        $this->requests[] = $request;
    }

    /**
     * Removes a request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $request The request.
     */
    public function removeRequest(InternalRequestInterface $request)
    {
        unset($this->requests[array_search($request, $this->requests, true)]);
        $this->requests = array_values($this->requests);
    }
}
