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

use Ivory\HttpAdapter\ConfigurationInterface;
use Ivory\HttpAdapter\Extractor\ProtocolVersionExtractor;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Normalizer\BodyNormalizer;

/**
 * Cake http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CakeHttpAdapter extends AbstractHttpAdapter
{
    /** @var \HttpSocket */
    private $httpSocket;

    /**
     * Creates a Cake http adapter.
     *
     * @param \HttpSocket|null                               $httpSocket    The Cake http socket.
     * @param \Ivory\HttpAdapter\ConfigurationInterface|null $configuration The configuration.
     */
    public function __construct(\HttpSocket $httpSocket = null, ConfigurationInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->httpSocket = $httpSocket ?: new \HttpSocket();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cake';
    }

    /**
     * {@inheritdoc}
     */
    protected function doSend(InternalRequestInterface $internalRequest)
    {
        $url = (string) $internalRequest->getUrl();
        $this->httpSocket->config['timeout'] = $this->getConfiguration()->getTimeout();

        try {
            $response = $this->httpSocket->request(array(
                'version'  => $this->getConfiguration()->getProtocolVersion(),
                'redirect' => false,
                'uri'      => $url,
                'method'   => $internalRequest->getMethod(),
                'header'   => $this->prepareHeaders($internalRequest),
                'body'     => $this->prepareBody($internalRequest),
            ));
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), $e->getMessage());
        }

        if (($error = $this->httpSocket->lastError()) !== null) {
            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), $error);
        }

        return $this->createResponse(
            ProtocolVersionExtractor::extract($response->httpVersion),
            (integer) $response->code,
            $response->reasonPhrase,
            $response->headers,
            BodyNormalizer::normalize($response->body, $internalRequest->getMethod())
        );
    }
}
