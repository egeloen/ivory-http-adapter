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

use http\Client;
use http\Client\Request;
use http\Message\Body;
use Ivory\HttpAdapter\Message\InternalRequestInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PeclHttpAdapter extends AbstractHttpAdapter
{
    /** @var \http\Client */
    private $client;

    /**
     * Creates a pecl http adapter.
     *
     * @param \http\Client                              $client
     * @param \Ivory\HttpAdapter\ConfigurationInterface $configuration
     */
    public function __construct(Client $client = null, ConfigurationInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->client = $client ?: new Client();
    }

    /**
     * {@inheritdoc}
     */
    protected function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        $body = new Body();
        $body->append($this->prepareBody($internalRequest));

        $request = new Request(
            $internalRequest->getMethod(),
            $uri = (string) $internalRequest->getUri(),
            $this->prepareHeaders($internalRequest),
            $body
        );

        $httpVersion = $internalRequest->getProtocolVersion() === InternalRequestInterface::PROTOCOL_VERSION_1_0
            ? \http\Client\Curl\HTTP_VERSION_1_0
            : \http\Client\Curl\HTTP_VERSION_1_1;

        $request->setOptions(array(
            'protocol' => $httpVersion,
            'timeout'  => $this->getConfiguration()->getTimeout(),
        ));

        try {
            $this->client->reset()->enqueue($request)->send();
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUri($uri, $this->getName(), $e->getMessage());
        }

        $response = $this->client->getResponse();

        return $this->getConfiguration()->getMessageFactory()->createResponse(
            $response->getResponseCode(),
            $response->getHttpVersion(),
            $response->getHeaders(),
            $response->getBody()->getResource()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pecl_http';
    }
}
