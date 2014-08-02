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

use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Zend\Http\Client;
use Zend\Http\Response\Stream;

/**
 * Zend 2 http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Zend2HttpAdapter extends AbstractHttpAdapter
{
    /** @var \Zend\Http\Client */
    protected $client;

    /**
     * Creates a zend 2 http adapter.
     *
     * @param \Zend\Http\Client                                       $client         The zend 2 client.
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface|null $messageFactory The message factory.
     */
    public function __construct(Client $client = null, MessageFactoryInterface $messageFactory = null)
    {
        parent::__construct($messageFactory);

        $this->client = $client ?: new Client();
    }

    /**
     * {@inheritdoc}
     */
    protected function doSend($url, $method, array $headers = array(), $data = array(), array $files = array())
    {
        $this->client
            ->resetParameters(true)
            ->setOptions(array(
                'httpversion'  => $this->protocolVersion,
                'maxredirects' => $this->maxRedirects,
            ))
            ->setUri($this->prepareUrl($url))
            ->setMethod($this->prepareMethod($method))
            ->setHeaders($this->prepareHeaders($headers));

        if (is_string($data)) {
            $this->client->setRawBody($data);
        } else {
            $this->client->setParameterPost($data);
        }

        foreach ($files as $key => $file) {
            $this->client->setFileUpload($file, $key);
        }

        try {
            $response = $this->client->send();
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), $e->getMessage());
        }

        if ($this->hasMaxRedirects() && $this->client->getRedirectionsCount() > $this->maxRedirects) {
            throw HttpAdapterException::maxRedirectsExceeded($url, $this->maxRedirects, $this->getName());
        }

        return $this->createResponse(
            $response->getVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $method,
            $response->getHeaders()->toArray(),
            function () use ($response) {
                return $response instanceof Stream ? $response->getStream() : $response->getBody();
            },
            $url
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'zend2';
    }
}
