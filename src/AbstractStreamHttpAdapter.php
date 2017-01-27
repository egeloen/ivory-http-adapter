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
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractStreamHttpAdapter extends AbstractHttpAdapter
{
    /**
     * {@inheritdoc}
     */
    protected function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        $context = stream_context_create([
            'http' => [
                'follow_location'  => false,
                'max_redirects'    => 1,
                'ignore_errors'    => true,
                'timeout'          => $this->getConfiguration()->getTimeout(),
                'protocol_version' => $internalRequest->getProtocolVersion(),
                'method'           => $internalRequest->getMethod(),
                'header'           => $this->prepareHeaders($internalRequest, false),
                'content'          => $this->prepareBody($internalRequest),
            ],
        ]);

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
     * @param string   $uri
     * @param resource $context
     *
     * @return array
     */
    abstract protected function process($uri, $context);
}
