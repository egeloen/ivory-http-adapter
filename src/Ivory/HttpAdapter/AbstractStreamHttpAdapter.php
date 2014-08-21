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
use Ivory\HttpAdapter\Normalizer\BodyNormalizer;
use Ivory\HttpAdapter\Normalizer\HeadersNormalizer;

/**
 * Abstract stream http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractStreamHttpAdapter extends AbstractHttpAdapter
{
    /**
     * {@inhertdoc}
     */
    protected function doSend(InternalRequestInterface $internalRequest)
    {
        $context = stream_context_create(array(
            'http' => array(
                'follow_location'  => false,
                'max_redirects'    => 1,
                'ignore_errors'    => true,
                'timeout'          => $this->timeout,
                'protocol_version' => $internalRequest->getProtocolVersion(),
                'method'           => $internalRequest->getMethod(),
                'header'           => $this->prepareHeaders($internalRequest, false),
                'content'          => $this->prepareBody($internalRequest),
            )
        ));

        list($body, $headers) = $this->process($internalRequest->getUrl(), $context);

        if ($body === false) {
            throw HttpAdapterException::cannotFetchUrl(
                $internalRequest->getUrl(),
                $this->getName(),
                print_r(error_get_last(), true)
            );
        }

        return $this->createResponse(
            ProtocolVersionExtractor::extract($headers),
            StatusCodeExtractor::extract($headers),
            ReasonPhraseExtractor::extract($headers),
            HeadersNormalizer::normalize($headers),
            BodyNormalizer::normalize($body, $internalRequest->getMethod())
        );
    }

    /**
     * Processes the url/context.
     *
     * @param string   $url     The url.
     * @param resource $context The context.
     *
     * @return array The processed url/context (0 => body, 1 => headers).
     */
    abstract protected function process($url, $context);
}
