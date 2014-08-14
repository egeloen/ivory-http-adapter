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
use Ivory\HttpAdapter\Parser\ProtocolVersionParser;
use Ivory\HttpAdapter\Parser\ReasonPhraseParser;
use Httpful\Mime;
use Httpful\Request;

/**
 * Httpful http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpfulHttpAdapter extends AbstractCurlHttpAdapter
{
    /**
     * Creates an httpful http adapter.
     */
    public function __construct()
    {
        parent::__construct();
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
    protected function doSend(InternalRequestInterface $internalRequest)
    {
        $request = Request::init($internalRequest->getMethod())
            ->whenError(function () {})
            ->addOnCurlOption(CURLOPT_HTTP_VERSION, $this->prepareProtocolVersion($internalRequest))
            ->timeout($this->timeout)
            ->followRedirects($this->maxRedirects)
            ->uri($internalRequest->getUrl())
            ->addHeaders($this->prepareHeaders($internalRequest))
            ->body($this->prepareContent($internalRequest));

        if ($internalRequest->hasFiles()) {
            $request->mime(Mime::UPLOAD);
        }

        try {
            $response = $request->send();
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUrl($internalRequest->getUrl(), $this->getName(), $e->getMessage());
        }

        return $this->createResponse(
            ProtocolVersionParser::parse($response->raw_headers),
            $response->code,
            ReasonPhraseParser::parse($response->raw_headers),
            $response->headers->toArray(),
            BodyNormalizer::normalize($response->body, $internalRequest->getMethod()),
            $internalRequest->getUrl()
        );
    }
}
