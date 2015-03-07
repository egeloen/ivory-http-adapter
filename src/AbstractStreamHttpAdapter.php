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
     * {@inheritdoc}
     */
    protected function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        $context = stream_context_create(array(
            'http' => array(
                'follow_location'  => false,
                'max_redirects'    => 1,
                'ignore_errors'    => true,
                'timeout'          => $this->getConfiguration()->getTimeout(),
                'protocol_version' => $internalRequest->getProtocolVersion(),
                'method'           => $internalRequest->getMethod(),
                'header'           => $this->prepareHeaders($internalRequest, false),
                'content'          => $this->prepareBody($internalRequest),
            ),
        ));

        list($body, $headers) = $this->process($uri = (string) $internalRequest->getUri(), $context);

        if ($body === false) {
            $error = error_get_last();
            throw HttpAdapterException::cannotFetchUri($uri, $this->getName(), $error['message']);
        }

        return $this->getConfiguration()->getMessageFactory()->createResponse(
            StatusCodeExtractor::extract($headers),
            ProtocolVersionExtractor::extract($headers),
            HeadersNormalizer::normalize($headers),
            BodyNormalizer::normalize($body, $internalRequest->getMethod())
        );
    }

    /**
     * Processes the uri/context.
     *
     * @param string   $uri     The uri.
     * @param resource $context The context.
     *
     * @return array The processed uri/context (0 => body, 1 => headers).
     */
    abstract protected function process($uri, $context);
}
