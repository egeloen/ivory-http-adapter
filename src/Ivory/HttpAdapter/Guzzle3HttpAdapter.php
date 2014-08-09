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
    protected function doSend(InternalRequestInterface $internalRequest)
    {
        $request = $this->client->createRequest(
            $internalRequest->getMethod(),
            $internalRequest->getUrl(),
            $this->prepareHeaders($internalRequest),
            $internalRequest->getData(),
            array('timeout' => $this->timeout)
        );

        foreach ($internalRequest->getFiles() as $key => $file) {
            $request->addPostFile($key, $file);
        }

        $request->setProtocolVersion($internalRequest->getProtocolVersion());
        $request->getParams()->set('redirect.disable', !$this->hasMaxRedirects());
        $request->getParams()->set('redirect.max', $this->maxRedirects);

        try {
            $response = $request->send();
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUrl($internalRequest->getUrl(), $this->getName(), $e->getMessage());
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
            ),
            $response->getEffectiveUrl()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'guzzle3';
    }
}
