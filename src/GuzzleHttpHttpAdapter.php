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
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\Stream\GuzzleHttpStream;
use Ivory\HttpAdapter\Normalizer\BodyNormalizer;

/**
 * Guzzle http http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class GuzzleHttpHttpAdapter extends AbstractCurlHttpAdapter
{
    /** @var \GuzzleHttp\ClientInterface */
    protected $client;

    /**
     * Creates a guzzle http http adapter.
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
        return 'guzzle_http';
    }

    /**
     * {@inheritdoc}
     */
    protected function doSend(InternalRequestInterface $internalRequest)
    {
        $url = (string) $internalRequest->getUrl();

        $request = $this->client->createRequest(
            $internalRequest->getMethod(),
            $url,
            array(
                'exceptions'      => false,
                'allow_redirects' => false,
                'timeout'         => $this->configuration->getTimeout(),
                'connect_timeout' => $this->configuration->getTimeout(),
                'version'         => $internalRequest->getProtocolVersion(),
                'headers'         => $this->prepareHeaders($internalRequest),
                'body'            => $this->prepareContent($internalRequest),
            )
        );

        try {
            $response = $this->client->send($request);
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), $e->getMessage());
        }

        return $this->createResponse(
            $response->getProtocolVersion(),
            (integer) $response->getStatusCode(),
            $response->getReasonPhrase(),
            $response->getHeaders(),
            BodyNormalizer::normalize(
                function () use ($response) {
                    return new GuzzleHttpStream($response->getBody());
                },
                $internalRequest->getMethod()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createFile($file)
    {
        return fopen($file, 'r');
    }
}
