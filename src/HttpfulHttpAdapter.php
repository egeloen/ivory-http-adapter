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

use Httpful\Mime;
use Httpful\Request;
use Ivory\HttpAdapter\Extractor\ProtocolVersionExtractor;
use Ivory\HttpAdapter\Extractor\ReasonPhraseExtractor;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Normalizer\BodyNormalizer;

/**
 * Httpful http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpfulHttpAdapter extends AbstractCurlHttpAdapter
{
    /**
     * Creates an httpful http adapter.
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
        return 'httpful';
    }

    /**
     * {@inheritdoc}
     */
    protected function doSendInternalRequest(InternalRequestInterface $internalRequest)
    {
        $request = Request::init($internalRequest->getMethod())
            ->whenError(function () {})
            ->addOnCurlOption(CURLOPT_HTTP_VERSION, $this->prepareProtocolVersion($internalRequest))
            ->timeout($this->getConfiguration()->getTimeout())
            ->uri($url = (string) $internalRequest->getUrl())
            ->addHeaders($this->prepareHeaders($internalRequest))
            ->body($this->prepareContent($internalRequest));

        if (defined('CURLOPT_CONNECTTIMEOUT_MS')) {
            $request->addOnCurlOption(CURLOPT_CONNECTTIMEOUT_MS, $this->getConfiguration()->getTimeout() * 1000);
        } else { // @codeCoverageIgnoreStart
            $request->addOnCurlOption(CURLOPT_CONNECTTIMEOUT, $this->getConfiguration()->getTimeout());
        } // @codeCoverageIgnoreEnd

        if ($internalRequest->hasFiles()) {
            $request->mime(Mime::UPLOAD);
        }

        try {
            $response = $request->send();
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), $e->getMessage());
        }

        return $this->getConfiguration()->getMessageFactory()->createResponse(
            $response->code,
            ReasonPhraseExtractor::extract($response->raw_headers),
            ProtocolVersionExtractor::extract($response->raw_headers),
            $response->headers->toArray(),
            BodyNormalizer::normalize($response->body, $internalRequest->getMethod())
        );
    }
}
