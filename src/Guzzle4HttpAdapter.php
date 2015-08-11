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
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Normalizer\BodyNormalizer;

/**
 * Guzzle 4 http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Guzzle4HttpAdapter extends AbstractCurlHttpAdapter
{
    /** @var \GuzzleHttp\ClientInterface */
    private $client;

    /**
     * Creates a guzzle 5 http adapter.
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
        return 'guzzle4';
    }

    /**
     * {@inheritdoc}
     */
    protected function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        try {
            $response = $this->client->send($this->createRequest($internalRequest));
        } catch (RequestException $e) {
            throw HttpAdapterException::cannotFetchUri(
                $e->getRequest()->getUrl(),
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
        foreach ($internalRequests as $internalRequest) {
            $requests[] = $this->createRequest($internalRequest, $success, $error);
        }

        class_exists('GuzzleHttp\Pool')
            ? Pool::batch($this->client, $requests)
            : \GuzzleHttp\batch($this->client, $requests);
    }

    /**
     * {@inheritdoc}
     */
    protected function createFile($file)
    {
        return fopen($file, 'r');
    }

    /**
     * Creates a request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     * @param callable|null                                       $success         The success callable.
     * @param callable|null                                       $error           The error callable.
     *
     * @return \GuzzleHttp\Message\RequestInterface The request.
     */
    private function createRequest(InternalRequestInterface $internalRequest, $success = null, $error = null)
    {
        $request = $this->client->createRequest(
            $internalRequest->getMethod(),
            (string) $internalRequest->getUri(),
            array(
                'exceptions'      => false,
                'allow_redirects' => false,
                'timeout'         => $this->getConfiguration()->getTimeout(),
                'connect_timeout' => $this->getConfiguration()->getTimeout(),
                'version'         => $internalRequest->getProtocolVersion(),
                'headers'         => $this->prepareHeaders($internalRequest),
                'body'            => $this->prepareContent($internalRequest),
            )
        );

        if (is_callable($success)) {
            $messageFactory = $this->getConfiguration()->getMessageFactory();

            $request->getEmitter()->on(
                'complete',
                function (CompleteEvent $event) use ($success, $internalRequest, $messageFactory) {
                    $response = $messageFactory->createResponse(
                        (integer) $event->getResponse()->getStatusCode(),
                        $event->getResponse()->getProtocolVersion(),
                        $event->getResponse()->getHeaders(),
                        BodyNormalizer::normalize(
                            function () use ($event) {
                                return $event->getResponse()->getBody()->detach();
                            },
                            $internalRequest->getMethod()
                        )
                    );

                    $response = $response->withParameter('request', $internalRequest);
                    call_user_func($success, $response);
                }
            );
        }

        if (is_callable($error)) {
            $httpAdapterName = $this->getName();

            $request->getEmitter()->on(
                'error',
                function (ErrorEvent $event) use ($error, $internalRequest, $httpAdapterName) {
                    $exception = HttpAdapterException::cannotFetchUri(
                        $event->getException()->getRequest()->getUrl(),
                        $httpAdapterName,
                        $event->getException()->getMessage()
                    );
                    $exception->setRequest($internalRequest);
                    call_user_func($error, $exception);
                }
            );
        }

        return $request;
    }
}
