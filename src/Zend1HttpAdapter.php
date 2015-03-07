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

use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Normalizer\BodyNormalizer;

/**
 * Zend 1 http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Zend1HttpAdapter extends AbstractHttpAdapter
{
    /** @var \Zend_Http_Client */
    private $client;

    /**
     * Creates a zend 1 http adapter.
     *
     * @param \Zend_Http_Client|null                         $client        The zend 1 client.
     * @param \Ivory\HttpAdapter\ConfigurationInterface|null $configuration The configuration.
     */
    public function __construct(\Zend_Http_Client $client = null, ConfigurationInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->client = $client ?: new \Zend_Http_Client();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'zend1';
    }

    /**
     * {@inheritdoc}
     */
    protected function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        $this->client
            ->resetParameters(true)
            ->setConfig(array(
                'httpversion'  => $internalRequest->getProtocolVersion(),
                'timeout'      => $this->getConfiguration()->getTimeout(),
                'maxredirects' => 0,
            ))
            ->setUri($uri = (string) $internalRequest->getUri())
            ->setMethod($internalRequest->getMethod())
            ->setHeaders($this->prepareHeaders($internalRequest))
            ->setRawData($this->prepareBody($internalRequest));

        try {
            $response = $this->client->request();
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUri($uri, $this->getName(), $e->getMessage());
        }

        return $this->getConfiguration()->getMessageFactory()->createResponse(
            $response->getStatus(),
            $response->getVersion(),
            $response->getHeaders(),
            BodyNormalizer::normalize(
                function () use ($response) {
                    return $response instanceof \Zend_Http_Response_Stream
                        ? $response->getStream()
                        : $response->getBody();
                },
                $internalRequest->getMethod()
            )
        );
    }
}
