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
use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Ivory\HttpAdapter\Message\Stream\Guzzle3Stream;

/**
 * Guzzle 3 http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Guzzle3HttpAdapter extends AbstractHttpAdapter
{
    /** @var \Guzzle\Http\ClientInterface */
    protected $client;

    /**
     * Creates a guzzle 3 http adapter.
     *
     * @param \Guzzle\Http\ClientInterface                            $client         The guzzle 3 client.
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface|null $messageFactory The message factory.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the curl extension is not loaded.
     */
    public function __construct(ClientInterface $client = null, MessageFactoryInterface $messageFactory = null)
    {
        if (!function_exists('curl_init')) {
            throw HttpAdapterException::extensionIsNotLoaded('curl', $this->getName());
        }

        parent::__construct($messageFactory);

        $this->client = $client ?: new Client();
    }

    /**
     * {@inheritdoc}
     */
    protected function doSend($url, $method, array $headers = array(), $data = array(), array $files = array())
    {
        $request = $this->client->createRequest(
            $this->prepareMethod($method),
            $this->prepareUrl($url),
            $this->prepareHeaders($headers, $data, $files),
            $data
        );

        foreach ($files as $key => $file) {
            $request->addPostFile($key, $file);
        }

        $request->setProtocolVersion($this->protocolVersion);
        $request->getParams()->set('redirect.disable', !$this->hasMaxRedirects());
        $request->getParams()->set('redirect.max', $this->maxRedirects);

        try {
            $response = $request->send();
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), $e->getMessage());
        }

        return $this->createResponse(
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $method,
            $response->getHeaders()->toArray(),
            function () use ($response) {
                return new Guzzle3Stream($response->getBody());
            },
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
