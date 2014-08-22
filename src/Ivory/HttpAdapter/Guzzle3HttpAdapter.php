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
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\Stream\Guzzle3Stream;
use Ivory\HttpAdapter\Normalizer\BodyNormalizer;

/**
 * Guzzle 3 http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Guzzle3HttpAdapter extends AbstractCurlHttpAdapter
{
    /** @var \Guzzle\Http\ClientInterface */
    protected $client;

    /**
     * Creates a guzzle 3 http adapter.
     *
     * @param \Guzzle\Http\ClientInterface $client The guzzle 3 client.
     */
    public function __construct(ClientInterface $client = null)
    {
        parent::__construct();

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
    protected function doSend(InternalRequestInterface $internalRequest)
    {
        $url = (string) $internalRequest->getUrl();

        $request = $this->client->createRequest(
            $internalRequest->getMethod(),
            $url,
            $this->prepareHeaders($internalRequest),
            $this->prepareContent($internalRequest),
            array(
                'exceptions'      => false,
                'allow_redirects' => false,
                'timeout'         => $this->timeout,
            )
        );

        $request->setProtocolVersion($internalRequest->getProtocolVersion());

        try {
            $response = $request->send();
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), $e->getMessage());
        }

        return $this->createResponse(
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $response->getHeaders()->toArray(),
            BodyNormalizer::normalize(
                function () use ($response) {
                    return new Guzzle3Stream($response->getBody());
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
        return '@'.$file;
    }
}
