<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter;

use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Mock http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 * @author Timothée Barray <tim@amicalement-web.net>
 */
class MockHttpAdapter extends AbstractHttpAdapter
{
    /** @var array */
    private $queuedResponses = array();

    /** @var array */
    private $receivedRequests = array();

    /**
     * Clears the requests stack and the response queue.
     */
    public function reset()
    {
        $this->receivedRequests = array();
        $this->queuedResponses = array();
    }

    /**
     * Gets all requests sent.
     *
     * @return array
     */
    public function getReceivedRequests()
    {
        return $this->receivedRequests;
    }

    /**
     * Gets currently queued responses.
     *
     * @return array
     */
    public function getQueuedResponses()
    {
        return $this->queuedResponses;
    }

    /**
     * Appends a response to the queue.
     *
     * Next request sent will get the first response of the queue.
     *
     * @param ResponseInterface $response
     */
    public function appendResponse(ResponseInterface $response)
    {
        $this->queuedResponses[] = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mock';
    }

    /**
     * {@inheritdoc}
     */
    protected function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        if (count($this->queuedResponses) <= 0) {
            throw new \OutOfBoundsException('You must append a response in the queue before sending a request.');
        }

        $this->receivedRequests[] = $internalRequest;

        return array_shift($this->queuedResponses);
    }
}
