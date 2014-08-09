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
use Ivory\HttpAdapter\Message\Stream\Guzzle4Stream;
use Ivory\HttpAdapter\Normalizer\BodyNormalizer;

/**
 * Guzzle 4 http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Guzzle4HttpAdapter extends AbstractCurlHttpAdapter
{
    /** @var \GuzzleHttp\ClientInterface */
    protected $client;

    /**
     * Creates a guzzle 4 http adapter.
     *
     * @param \GuzzleHttp\ClientInterface|null $client The guzzle 4 client.
     */
    public function __construct(ClientInterface $client = null)
    {
        parent::__construct();

        $this->client = $client ?: new Client();
    }

    /**
     * {@inheritdoc}
     */
    protected function doSend(InternalRequestInterface $internalRequest)
    {
        $data = $internalRequest->getData();

        foreach ($internalRequest->getFiles() as $name => $file) {
            $data[$name] = fopen($file, 'r');
        }

        $request = $this->client->createRequest(
            $internalRequest->getMethod(),
            $internalRequest->getUrl(),
            array(
                'version'         => $internalRequest->getProtocolVersion(),
                'timeout'         => $this->timeout,
                'allow_redirects' => $this->hasMaxRedirects() ? array('max' => $this->getMaxRedirects()) : false,
                'headers'         => $this->prepareHeaders($internalRequest),
                'body'            => $data,
            )
        );

        try {
            $response = $this->client->send($request);
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUrl($internalRequest->getUrl(), $this->getName(), $e->getMessage());
        }

        return $this->createResponse(
            $response->getProtocolVersion(),
            (integer) $response->getStatusCode(),
            $response->getReasonPhrase(),
            $response->getHeaders(),
            BodyNormalizer::normalize(
                function () use ($response) {
                    return new Guzzle4Stream($response->getBody());
                },
                $internalRequest->getMethod()
            ),
            $response->getEffectiveUrl()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'guzzle4';
    }
}
