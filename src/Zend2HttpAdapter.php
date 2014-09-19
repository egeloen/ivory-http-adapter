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
     * @param \Zend\Http\Client|null                         $client        The zend 2 client.
     * @param \Ivory\HttpAdapter\ConfigurationInterface|null $configuration The configuration.
     */
    public function __construct(Client $client = null, ConfigurationInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->client = $client ?: new Client();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'zend2';
    }

    /**
     * {@inheritdoc}
     */
    protected function doSend(InternalRequestInterface $internalRequest)
    {
        $url = (string) $internalRequest->getUrl();

        $this->client
            ->resetParameters(true)
            ->setOptions(array(
                'httpversion'  => $internalRequest->getProtocolVersion(),
                'timeout'      => $this->configuration->getTimeout(),
                'maxredirects' => 0,
            ))
            ->setUri($url)
            ->setMethod($internalRequest->getMethod())
            ->setHeaders($this->prepareHeaders($internalRequest))
            ->setRawBody($this->prepareBody($internalRequest));

        try {
            $response = $this->client->send();
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), $e->getMessage());
        }

        return $this->createResponse(
            $response->getVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $response->getHeaders()->toArray(),
            BodyNormalizer::normalize(
                function () use ($response) {
                    return $response instanceof Stream ? $response->getStream() : $response->getBody();
                },
                $internalRequest->getMethod()
            )
        );
    }
}
