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
        parent::__construct(false);

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
    protected function doSend(InternalRequestInterface $internalRequest)
    {
        $url = (string) $internalRequest->getUrl();

        $request = $this->client->createRequest(
            $internalRequest->getMethod(),
            $url,
            array(
                'version'         => $internalRequest->getProtocolVersion(),
                'timeout'         => $this->timeout,
                'allow_redirects' => false,
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
                    return new Guzzle4Stream($response->getBody());
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
