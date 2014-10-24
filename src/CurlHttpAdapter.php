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

use Ivory\HttpAdapter\Extractor\ProtocolVersionExtractor;
use Ivory\HttpAdapter\Extractor\ReasonPhraseExtractor;
use Ivory\HttpAdapter\Extractor\StatusCodeExtractor;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\RequestInterface;
use Ivory\HttpAdapter\Normalizer\BodyNormalizer;
use Ivory\HttpAdapter\Normalizer\HeadersNormalizer;

/**
 * Curl http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CurlHttpAdapter extends AbstractCurlHttpAdapter
{
    /**
     * Creates a curl http adapter.
     *
     * @param \Ivory\HttpAdapter\ConfigurationInterface|null $configuration The configuration.
     */
    public function __construct(ConfigurationInterface $configuration = null)
    {
        parent::__construct($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'curl';
    }

    /**
     * {@inheritdoc}
     */
    protected function doSend(InternalRequestInterface $internalRequest)
    {
        $curl = curl_init();

        $url = (string) $internalRequest->getUrl();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, $this->prepareProtocolVersion($internalRequest));
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->prepareHeaders($internalRequest, false, false));

        $this->configureTimeout($curl, 'CURLOPT_TIMEOUT');
        $this->configureTimeout($curl, 'CURLOPT_CONNECTTIMEOUT');

        if ($internalRequest->hasFiles() && $this->isSafeUpload()) {
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
        }

        switch ($internalRequest->getMethod()) {
            case RequestInterface::METHOD_HEAD:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $internalRequest->getMethod());
                curl_setopt($curl, CURLOPT_NOBODY, true);
                break;

            case RequestInterface::METHOD_TRACE:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $internalRequest->getMethod());
                break;

            case RequestInterface::METHOD_POST:
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $this->prepareContent($internalRequest));
                break;

            case RequestInterface::METHOD_PUT:
            case RequestInterface::METHOD_PATCH:
            case RequestInterface::METHOD_DELETE:
            case RequestInterface::METHOD_OPTIONS:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $internalRequest->getMethod());
                curl_setopt($curl, CURLOPT_POSTFIELDS, $this->prepareContent($internalRequest));
                break;
        }

        if (($response = curl_exec($curl)) === false) {
            $error = curl_error($curl);
            curl_close($curl);

            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), $error);
        }

        $headersSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        curl_close($curl);

        $headers = substr($response, 0, $headersSize);
        $body = substr($response, $headersSize);

        return $this->createResponse(
            ProtocolVersionExtractor::extract($headers),
            StatusCodeExtractor::extract($headers),
            ReasonPhraseExtractor::extract($headers),
            HeadersNormalizer::normalize($headers),
            BodyNormalizer::normalize($body, $internalRequest->getMethod())
        );
    }

    /**
     * Configures a timeout.
     *
     * @param resource $curl The curl resource.
     * @param string   $type The timeout type.
     */
    private function configureTimeout($curl, $type)
    {
        if (defined($type.'_MS')) {
            curl_setopt($curl, constant($type.'_MS'), $this->configuration->getTimeout() * 1000);
        } else { // @codeCoverageIgnoreStart
            curl_setopt($curl, constant($type), $this->configuration->getTimeout());
        } // @codeCoverageIgnoreEnd
    }
}
