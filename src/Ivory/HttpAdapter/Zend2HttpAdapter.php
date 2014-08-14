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
     * @param \Zend\Http\Client $client The zend 2 client.
     */
    public function __construct(Client $client = null)
    {
        parent::__construct();

        $this->client = $client ?: new Client();
    }

    /**
     * {@inheritdoc}
     */
    protected function doSend(InternalRequestInterface $internalRequest)
    {
        $this->client
            ->resetParameters(true)
            ->setOptions(array(
                'httpversion'  => $internalRequest->getProtocolVersion(),
                'timeout'      => $this->timeout,
                'maxredirects' => $this->maxRedirects,
            ))
            ->setUri($internalRequest->getUrl())
            ->setMethod($internalRequest->getMethod())
            ->setHeaders($this->prepareHeaders($internalRequest))
            ->setRawBody($this->prepareBody($internalRequest));

        try {
            $response = $this->client->send();
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUrl($internalRequest->getUrl(), $this->getName(), $e->getMessage());
        }

        if ($this->hasMaxRedirects() && $this->client->getRedirectionsCount() > $this->maxRedirects) {
            throw HttpAdapterException::maxRedirectsExceeded(
                $internalRequest->getUrl(),
                $this->maxRedirects,
                $this->getName()
            );
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
            ),
            $internalRequest->getUrl()
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
