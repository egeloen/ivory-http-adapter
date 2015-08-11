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

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Normalizer\BodyNormalizer;

/**
 * Guzzle 6 http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Guzzle6HttpAdapter extends AbstractHttpAdapter
{
    /** @var \GuzzleHttp\ClientInterface */
    private $client;

    /**
     * Creates a guzzle 6 http adapter.
     *
     * @param \GuzzleHttp\ClientInterface|null               $client        The guzzle 4 client.
     * @param \Ivory\HttpAdapter\ConfigurationInterface|null $configuration The configuration.
     */
    public function __construct(ClientInterface $client = null, ConfigurationInterface $configuration = null)
    {
        parent::__construct($configuration, false);

        $this->client = $client ?: new Client();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'guzzle6';
    }

    /**
     * {@inheritdoc}
     */
    protected function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        try {
            $response = $this->client->send(
                $this->createRequest($internalRequest),
                $this->createOptions($internalRequest)
            );
        } catch (RequestException $e) {
            throw HttpAdapterException::cannotFetchUri(
                $e->getRequest()->getUri(),
                $this->getName(),
                $e->getMessage()
            );
        }

        return $this->getConfiguration()->getMessageFactory()->createResponse(
            (integer) $response->getStatusCode(),
            $response->getProtocolVersion(),
            $response->getHeaders(),
            BodyNormalizer::normalize(
                function () use ($response) {
                    return $response->getBody()->detach();
                },
                $internalRequest->getMethod()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function sendInternalRequests(array $internalRequests, $success, $error)
    {
        $requests = array();
        foreach ($internalRequests as $key => $internalRequest) {
            $requests[$key] = $this->createRequest($internalRequest);
        }

        $httpAdapter = $this;

        $pool = new Pool($this->client, $requests, array_merge($this->createOptions(), array(
            'fulfilled' => function ($response, $index) use ($success, $internalRequests, $httpAdapter) {
                $response = $httpAdapter->getConfiguration()->getMessageFactory()->createResponse(
                    (integer) $response->getStatusCode(),
                    $response->getProtocolVersion(),
                    $response->getHeaders(),
                    BodyNormalizer::normalize(
                        function () use ($response) {
                            return $response->getBody()->detach();
                        },
                        $internalRequests[$index]->getMethod()
                    )
                );

                $response = $response->withParameter('request', $internalRequests[$index]);
                call_user_func($success, $response);
            },
            'rejected' => function ($exception, $index)  use ($error, $internalRequests, $httpAdapter) {
                $exception = HttpAdapterException::cannotFetchUri(
                    $exception->getRequest()->getUri(),
                    $httpAdapter->getName(),
                    $exception->getMessage()
                );

                $exception->setRequest($internalRequests[$index]);
                call_user_func($error, $exception);
            },
        )));

        $pool->promise()->wait();
    }

    /**
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    private function createRequest(InternalRequestInterface $internalRequest)
    {
        return new Request(
            $internalRequest->getMethod(),
            $internalRequest->getUri(),
            $this->prepareHeaders($internalRequest),
            $this->prepareBody($internalRequest),
            $internalRequest->getProtocolVersion()
        );
    }

    /**
     * @return array
     */
    private function createOptions()
    {
        return array(
            'http_errors'     => false,
            'allow_redirects' => false,
            'timeout'         => $this->getConfiguration()->getTimeout(),
            'connect_timeout' => $this->getConfiguration()->getTimeout(),
        );
    }
}
