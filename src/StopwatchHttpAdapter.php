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

use Psr\Http\Message\OutgoingRequestInterface;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Stopwatch http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StopwatchHttpAdapter extends AbstractHttpAdapterTemplate
{
    /** @var \Ivory\HttpAdapter\HttpAdapterInterface */
    private $httpAdapter;

    /** @var \Symfony\Component\Stopwatch\Stopwatch */
    private $stopwatch;

    /**
     * Creates a stopwatch event subscriber.
     *
     * @param \Ivory\HttpAdapter\HttpAdapterInterface $httpAdapter The http adapter.
     * @param \Symfony\Component\Stopwatch\Stopwatch  $stopwatch   The stopwatch.
     */
    public function __construct(HttpAdapterInterface $httpAdapter, Stopwatch $stopwatch)
    {
        $this->setHttpAdapter($httpAdapter);
        $this->setStopwatch($stopwatch);
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
     * Gets the stopwatch.
     *
     * @return \Symfony\Component\Stopwatch\Stopwatch The stopwatch.
     */
    public function getStopwatch()
    {
        return $this->stopwatch;
    }

    /**
     * Sets the stopwatch.
     *
     * @param \Symfony\Component\Stopwatch\Stopwatch $stopwatch The stopwatch.
     */
    public function setStopwatch(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->httpAdapter->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->httpAdapter->setConfiguration($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function send($url, $method, array $headers = array(), $datas = array(), array $files = array())
    {
        return $this->watch('send', array($url, $method, $headers, $datas, $files));
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequest(OutgoingRequestInterface $request)
    {
        return $this->watch('sendRequest', array($request));
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequests(array $requests, $success = null, $error = null)
    {
        return $this->watch('sendRequests', array($requests, $success, $error));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->httpAdapter->getName();
    }

    /**
     * Watches a method.
     *
     * @param string $method The method.
     * @param array  $params The parameters.
     *
     * @throws \Exception If an exception has been thrown during the HTTP adapter call.
     *
     * @return mixed The result.
     */
    private function watch($method, array $params = array())
    {
        $name = 'ivory.http_adapter';

        $this->stopwatch->start($name);

        try {
            $result = call_user_func_array(array($this->httpAdapter, $method), $params);
        } catch (\Exception $e) {
            $this->stopwatch->stop($name);

            throw $e;
        }

        $this->stopwatch->stop($name);

        return $result;
    }
}
