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
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Stopwatch http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StopwatchHttpAdapter extends PsrHttpAdapterDecorator
{
    /** @var \Symfony\Component\Stopwatch\Stopwatch */
    private $stopwatch;

    /**
     * Creates a stopwatch http adapter.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface $httpAdapter The http adapter.
     * @param \Symfony\Component\Stopwatch\Stopwatch  $stopwatch   The stopwatch.
     */
    public function __construct(HttpAdapterInterface $httpAdapter, Stopwatch $stopwatch)
    {
        parent::__construct($httpAdapter);

        $this->stopwatch = $stopwatch;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSendInternalRequest(InternalRequestInterface $internalRequest)
    {
        $this->stopwatch->start($name = 'ivory.http_adapter');

        try {
            $result = parent::doSendInternalRequest($internalRequest);
        } catch (\Exception $e) {
            $this->stopwatch->stop($name);

            throw $e;
        }

        $this->stopwatch->stop($name);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSendInternalRequests(array $internalRequests)
    {
        $this->stopwatch->start($name = 'ivory.http_adapter');

        try {
            $result = parent::doSendInternalRequests($internalRequests);
        } catch (\Exception $e) {
            $this->stopwatch->stop($name);

            throw $e;
        }

        $this->stopwatch->stop($name);

        return $result;
    }
}
