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

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PsrHttpAdapterDecorator implements HttpAdapterInterface
{
    use HttpAdapterTrait;

    /**
     * @var PsrHttpAdapterInterface
     */
    private $httpAdapter;

    /**
     * @param PsrHttpAdapterInterface $httpAdapter
     */
    public function __construct(PsrHttpAdapterInterface $httpAdapter)
    {
        $this->httpAdapter = $httpAdapter;
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
    public function getName()
    {
        return $this->httpAdapter->getName();
    }

    /**
     * {@inheritdoc}
     */
    protected function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        return $this->doSendInternalRequest($internalRequest);
    }

    /**
     * {@inheritdoc}
     */
    protected function sendInternalRequests(array $internalRequests, $success, $error)
    {
        $exceptions = [];

        try {
            $responses = $this->doSendInternalRequests($internalRequests);
        } catch (MultiHttpAdapterException $e) {
            $responses = $e->getResponses();
            $exceptions = $e->getExceptions();
        }

        foreach ($responses as $response) {
            call_user_func($success, $response);
        }

        foreach ($exceptions as $exception) {
            call_user_func($error, $exception);
        }
    }

    /**
     * @param InternalRequestInterface $internalRequest
     *
     * @throws HttpAdapterException
     *
     * @return ResponseInterface
     */
    protected function doSendInternalRequest(InternalRequestInterface $internalRequest)
    {
        return $this->httpAdapter->sendRequest($internalRequest);
    }

    /**
     * @param array $internalRequests
     *
     * @throws MultiHttpAdapterException
     *
     * @return array
     */
    protected function doSendInternalRequests(array $internalRequests)
    {
        return $this->httpAdapter->sendRequests($internalRequests);
    }
}
