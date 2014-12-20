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

use Guzzle\Common\Event;
use Guzzle\Http\Client;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Exception\RequestException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\Stream\GuzzleStream;
use Ivory\HttpAdapter\Normalizer\BodyNormalizer;

/**
 * Guzzle http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class GuzzleHttpAdapter extends AbstractCurlHttpAdapter
{
    /** @var \Guzzle\Http\ClientInterface */
    private $client;

    /**
     * Creates a guzzle 3 http adapter.
     *
     * @param \Guzzle\Http\ClientInterface|null              $client        The guzzle 3 client.
     * @param \Ivory\HttpAdapter\ConfigurationInterface|null $configuration The configuration.
     */
    public function __construct(ClientInterface $client = null, ConfigurationInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->client = $client ?: new Client();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'guzzle';
    }

    /**
     * {@inheritdoc}
     */
    protected function doSendInternalRequest(InternalRequestInterface $internalRequest)
    {
        try {
            $response = $this->createRequest($internalRequest)->send();
        } catch (RequestException $e) {
            throw HttpAdapterException::cannotFetchUrl(
                $e->getRequest()->getUrl(),
                $this->getName(),
                $e->getMessage()
            );
        }

        return $this->getConfiguration()->getMessageFactory()->createResponse(
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $response->getProtocolVersion(),
            $response->getHeaders()->toArray(),
            BodyNormalizer::normalize(
                function () use ($response) {
                    return new GuzzleStream($response->getBody());
                },
                $internalRequest->getMethod()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function doSendInternalRequests(array $internalRequests, $success, $error)
    {
        $requests = array();
        foreach ($internalRequests as $internalRequest) {
            $requests[] = $this->createRequest($internalRequest, $success, $error);
        }

        try {
            $this->client->send($requests);
        } catch (\Exception $e) {
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createFile($file)
    {
        return '@'.$file;
    }

    /**
     * Creates a request.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     * @param callable|null                                       $success         The success callable.
     * @param callable|null                                       $error           The error callable.
     *
     * @return \Ivory\HttpAdapter\Message\RequestInterface The request.
     */
    private function createRequest(InternalRequestInterface $internalRequest, $success = null, $error = null)
    {
        $request = $this->client->createRequest(
            $internalRequest->getMethod(),
            (string) $internalRequest->getUrl(),
            $this->prepareHeaders($internalRequest),
            $this->prepareContent($internalRequest),
            array(
                'exceptions'      => false,
                'allow_redirects' => false,
                'timeout'         => $this->getConfiguration()->getTimeout(),
                'connect_timeout' => $this->getConfiguration()->getTimeout(),
            )
        );

        $request->setProtocolVersion($internalRequest->getProtocolVersion());

        if (is_callable($success)) {
            $messageFactory = $this->getConfiguration()->getMessageFactory();

            $request->getEventDispatcher()->addListener(
                'request.success',
                function (Event $event) use ($messageFactory, $success, $internalRequest) {
                    $response = $messageFactory->createResponse(
                        $event['response']->getStatusCode(),
                        $event['response']->getReasonPhrase(),
                        $event['response']->getProtocolVersion(),
                        $event['response']->getHeaders()->toArray(),
                        BodyNormalizer::normalize(
                            function () use ($event) {
                                return new GuzzleStream($event['response']->getBody());
                            },
                            $internalRequest->getMethod()
                        )
                    );

                    $response->setParameter('request', $internalRequest);
                    call_user_func($success, $response);
                }
            );
        }

        if (is_callable($error)) {
            $httpAdapterName = $this->getName();

            $request->getEventDispatcher()->addListener(
                'request.exception',
                function (Event $event) use ($error, $internalRequest, $httpAdapterName) {
                    $exception = HttpAdapterException::cannotFetchUrl(
                        $event['exception']->getRequest()->getUrl(),
                        $httpAdapterName,
                        $event['exception']->getMessage()
                    );

                    $exception->setRequest($internalRequest);
                    call_user_func($error, $exception);
                }
            );
        }

        return $request;
    }
}
