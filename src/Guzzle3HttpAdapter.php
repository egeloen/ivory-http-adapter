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
use Ivory\HttpAdapter\Normalizer\BodyNormalizer;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Guzzle3HttpAdapter extends AbstractCurlHttpAdapter
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface|null        $client
     * @param ConfigurationInterface|null $configuration
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
        return 'guzzle3';
    }

    /**
     * {@inheritdoc}
     */
    protected function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        try {
            $response = $this->createRequest($internalRequest)->send();
        } catch (RequestException $e) {
            throw HttpAdapterException::cannotFetchUri(
                $e->getRequest()->getUrl(),
                $this->getName(),
                $e->getMessage()
            );
        }

        return $this->getConfiguration()->getMessageFactory()->createResponse(
            $response->getStatusCode(),
            $response->getProtocolVersion(),
            $response->getHeaders()->toArray(),
            BodyNormalizer::normalize(
                function () use ($response) {
                    $resource = $response->getBody()->getStream();
                    $response->getBody()->detachStream();

                    return $resource;
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
        $requests = [];
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
     * @param InternalRequestInterface $internalRequest
     * @param callable|null            $success
     * @param callable|null            $error
     *
     * @return RequestInterface
     */
    private function createRequest(InternalRequestInterface $internalRequest, $success = null, $error = null)
    {
        $request = $this->client->createRequest(
            $internalRequest->getMethod(),
            (string) $internalRequest->getUri(),
            $this->prepareHeaders($internalRequest),
            $this->prepareContent($internalRequest),
            [
                'exceptions'      => false,
                'allow_redirects' => false,
                'timeout'         => $this->getConfiguration()->getTimeout(),
                'connect_timeout' => $this->getConfiguration()->getTimeout(),
            ]
        );

        $request->setProtocolVersion($internalRequest->getProtocolVersion());

        if (is_callable($success)) {
            $messageFactory = $this->getConfiguration()->getMessageFactory();

            $request->getEventDispatcher()->addListener(
                'request.success',
                function (Event $event) use ($messageFactory, $success, $internalRequest) {
                    $response = $messageFactory->createResponse(
                        $event['response']->getStatusCode(),
                        $event['response']->getProtocolVersion(),
                        $event['response']->getHeaders()->toArray(),
                        BodyNormalizer::normalize(
                            function () use ($event) {
                                $resource = $event['response']->getBody()->getStream();
                                $event['response']->getBody()->detachStream();

                                return $resource;
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

            $request->getEventDispatcher()->addListener(
                'request.exception',
                function (Event $event) use ($error, $internalRequest, $httpAdapterName) {
                    $exception = HttpAdapterException::cannotFetchUri(
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
