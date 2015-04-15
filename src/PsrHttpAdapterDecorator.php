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
 * PSR http adapter decorator.
 *
 * This decorator only decorates the PSR http adapter interface even if it implements the full http adapter interface.
 * This is done by design in order to inform you to use the final methods involved in the process.
 *
 * Concretely, that means it will never call the methods defined in the full http adapter interface but will always
 * call the methods defined in the PSR http adapter interface with your inputs converted into PSR requests in all
 * cases.
 *
 * In conclusion, if you want to decorate an http adapter, always implement the PSR http adapter interface or even
 * better use the http adapter template. Basically, the methods defined in the full http adapter interface are just
 * proxy ones to the PSR http adapter interface...
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PsrHttpAdapterDecorator implements HttpAdapterInterface
{
    use HttpAdapterTrait;

    /** @var \Ivory\HttpAdapter\PsrHttpAdapterInterface */
    private $httpAdapter;

    /**
     * Creates a PSR http adapter decorator.
     *
     * @param \Ivory\HttpAdapter\PsrHttpAdapterInterface $httpAdapter
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
        $exceptions = array();

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
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If an error occurred.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface The response.
     */
    protected function doSendInternalRequest(InternalRequestInterface $internalRequest)
    {
        return $this->httpAdapter->sendRequest($internalRequest);
    }

    /**
     * @param array $internalRequests The internal requests.
     *
     * @throws \Ivory\HttpAdapter\MultiHttpAdapterException If an error occurred.
     *
     * @return array The responses.
     */
    protected function doSendInternalRequests(array $internalRequests)
    {
        return $this->httpAdapter->sendRequests($internalRequests);
    }
}
