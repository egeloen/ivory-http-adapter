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
use Ivory\HttpAdapter\Normalizer\HeadersNormalizer;
use Ivory\HttpAdapter\Parser\EffectiveUrlParser;
use Ivory\HttpAdapter\Parser\ProtocolVersionParser;
use Ivory\HttpAdapter\Parser\ReasonPhraseParser;
use Ivory\HttpAdapter\Parser\StatusCodeParser;

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
                'protocol_version' => $internalRequest->getProtocolVersion(),
                'follow_location'  => $this->hasMaxRedirects(),
                'max_redirects'    => $this->maxRedirects + 1,
                'method'           => $internalRequest->getMethod(),
                'header'           => $this->prepareHeaders($internalRequest, false),
                'content'          => $this->prepareBody($internalRequest),
                'timeout'          => $this->timeout,
                'ignore_errors'    => !$this->hasMaxRedirects() && PHP_VERSION_ID === 50303,
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
            ProtocolVersionParser::parse($headers),
            StatusCodeParser::parse($headers),
            ReasonPhraseParser::parse($headers),
            HeadersNormalizer::normalize($headers),
            BodyNormalizer::normalize($body, $internalRequest->getMethod()),
            EffectiveUrlParser::parse($headers, $internalRequest->getUrl(), $this->hasMaxRedirects())
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
