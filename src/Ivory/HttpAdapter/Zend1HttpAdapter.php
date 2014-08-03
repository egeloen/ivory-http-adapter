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

use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Ivory\HttpAdapter\Message\RequestInterface;

/**
 * Zend 1 http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Zend1HttpAdapter extends AbstractHttpAdapter
{
    /** @var \Zend_Http_Client */
    protected $client;

    /**
     * Creates a zend 1 http adapter.
     *
     * @param \Zend_Http_Client                                  $client         The zend 1 client.
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface $messageFactory The message factory.
     */
    public function __construct(\Zend_Http_Client $client = null, MessageFactoryInterface $messageFactory = null)
    {
        parent::__construct($messageFactory);

        $this->client = $client ?: new Zend_Http_Client();
    }

    /**
     * {@inheritdoc}
     */
    protected function doSend($url, $method, array $headers, $data, array $files)
    {
        $this->client
            ->resetParameters(true)
            ->setConfig(array(
                'httpversion'  => $this->protocolVersion,
                'maxredirects' => $this->maxRedirects + 1,
            ))
            ->setUri($this->prepareUrl($url))
            ->setMethod($this->prepareMethod($method));

        if ($this->prepareMethod($method) !== RequestInterface::METHOD_POST || is_string($data)) {
            $this->client
                ->setHeaders($this->prepareHeaders($headers, $data, $files))
                ->setRawData($this->prepareData($data, $files));
        } else {
            $this->client
                ->setHeaders($this->prepareHeaders($headers))
                ->setParameterPost($data);

            foreach ($files as $name => $file) {
                $this->client->setFileUpload($file, $name);
            }
        }

        try {
            $response = $this->client->request();
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), $e->getMessage());
        }

        if ($this->hasMaxRedirects() && $this->client->getRedirectionsCount() > $this->maxRedirects) {
            throw HttpAdapterException::maxRedirectsExceeded($url, $this->maxRedirects, $this->getName());
        }

        return $this->createResponse(
            $response->getVersion(),
            $response->getStatus(),
            $response->getMessage(),
            $method,
            $response->getHeaders(),
            $response instanceof \Zend_Http_Client_Adapter_Stream ? $response->getStream() : $response->getBody(),
            $url
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'zend1';
    }
}
