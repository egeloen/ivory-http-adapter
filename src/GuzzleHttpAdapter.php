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

use Guzzle\Http\Client;
use Guzzle\Http\ClientInterface;
use Guzzle\Http\Exception\MultiTransferException;
use Guzzle\Http\Message\Response as GuzzleResponse;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\Request;
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
    protected function doSend(InternalRequestInterface $internalRequest)
    {
        $request = $this->createGuzzleRequest($internalRequest);

        try {
            $response = $request->send();
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUrl((string) $internalRequest->getUrl(), $this->getName(), $e->getMessage());
        }

        return $this->createResponseFromGuzzleResponse($response, $internalRequest->getMethod());
    }

    /**
     * {@inheritdoc}
     */
    protected function createFile($file)
    {
        return '@'.$file;
    }

    /**
     * @param \Psr\Http\Message\OutgoingRequestInterface[] $requests
     * @param null $success
     * @param null $error
     */
    protected function doSendMulti(array $requests, $success = null, $error = null)
    {
        foreach ($requests as &$request) {
            $request = $this->createGuzzleRequest($request);
        }

        try {
            $responses = $this->client->send($requests);
        } catch (MultiTransferException $e) {
            foreach ($e->getSuccessfulRequests() as $request) {
                if (is_callable($success)) {
                    $success(
                        $this->createResponseFromGuzzleResponse($request->getResponse(), $request->getMethod()),
                        $this->getConfiguration()->getMessageFactory()->createRequest(
                            $request->getUrl() //... TODO finish building proper request
                        )
                    );
                }
            }

            foreach ($e->getFailedRequests() as $request) {
                if (is_callable($error)) {
                    $error(new HttpAdapterException()); // TODO add request
                }
            }

            return;
        }

        foreach ($responses as $response) {
            if (is_callable($success)) {
                $success(
                    $this->createResponseFromGuzzleResponse($response, null), // TODO don't have access to original request here
                    new Request('http://foo.com') // TODO don't have access to original request here
                );
            }
        }
    }

    /**
     * @param InternalRequestInterface $internalRequest
     *
     * @return \Guzzle\Http\Message\RequestInterface
     */
    private function createGuzzleRequest(InternalRequestInterface $internalRequest)
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

        return $request;
    }

    /**
     * @param GuzzleResponse $response
     * @param string         $method
     *
     * @return Message\ResponseInterface
     */
    private function createResponseFromGuzzleResponse(GuzzleResponse $response, $method)
    {
        return $this->createResponse(
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $response->getHeaders()->toArray(),
            BodyNormalizer::normalize(
                function () use ($response) {
                    return new GuzzleStream($response->getBody());
                },
                $method
            )
        );
    }
}
