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

use Ivory\HttpAdapter\AbstractHttpAdapter;
use Ivory\HttpAdapter\ConfigurationInterface;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Psr\Http\Message\ResponseInterface;

class MockHttpAdapter extends AbstractHttpAdapter implements \Countable
{
    private $queuedResponses = [];

    private $receivedRequests = [];

    /**
     * Creates a mock http adapter.
     *
     * @param \Ivory\HttpAdapter\ConfigurationInterface|null $configuration The configuration.
     */
    public function __construct(ConfigurationInterface $configuration = null)
    {
        parent::__construct($configuration);
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
            throw new \OutOfBoundsException('Mock queue is empty');
        }

        $this->receivedRequests[] = $internalRequest;

        return array_shift($this->queuedResponses);
    }

    /**
     * Appends a response to the queue. Next request sent will get first response of the queue
     */
    public function appendResponse(ResponseInterface $response)
    {
        $this->queuedResponses[] = $response;
    }

    /**
     * Clears the requests stack and the response queue
     */
    public function reset()
    {
        $this->receivedRequests = [];
        $this->queuedResponses = [];
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->queuedResponses);
    }

    /**
     * Returns all requests sent
     */
    public function getReceivedRequests()
    {
        return $this->receivedRequests;
    }
}
