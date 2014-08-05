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
use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Ivory\HttpAdapter\Message\Stream\Guzzle4Stream;

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
     * @param \GuzzleHttp\ClientInterface|null                        $client         The guzzle 4 client.
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface|null $messageFactory The message factory.
     */
    public function __construct(ClientInterface $client = null, MessageFactoryInterface $messageFactory = null)
    {
        parent::__construct($messageFactory);

        $this->client = $client ?: new Client();
    }

    /**
     * {@inheritdoc}
     */
    protected function doSend($url, $method, array $headers, $data, array $files)
    {
        foreach ($files as $name => $file) {
            $data[$name] = fopen($file, 'r');
        }

        $request = $this->client->createRequest(
            $this->prepareMethod($method),
            $this->prepareUrl($url),
            array(
                'version'         => $this->protocolVersion,
                'allow_redirects' => $this->hasMaxRedirects() ? array('max' => $this->getMaxRedirects()) : false,
                'headers'         => $this->prepareHeaders($headers, $data, $files),
                'body'            => $data,
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
            $method,
            $response->getHeaders(),
            function () use ($response) {
                return new Guzzle4Stream($response->getBody());
            },
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
