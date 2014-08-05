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

use Httpful\Request;
use Ivory\HttpAdapter\Message\MessageFactoryInterface;

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
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface|null $messageFactory The message factory.
     */
    public function __construct(MessageFactoryInterface $messageFactory = null)
    {
        parent::__construct($messageFactory);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSend($url, $method, array $headers, $data, array $files)
    {
        $request = Request::init($this->prepareMethod($method))
            ->whenError(function () {})
            ->addOnCurlOption(CURLOPT_HTTP_VERSION, $this->prepareProtocolVersion($this->protocolVersion))
            ->followRedirects($this->getMaxRedirects())
            ->uri($this->prepareUrl($url))
            ->addHeaders($this->prepareHeaders($headers, $data, $files));

        if (empty($files)) {
            $request->body($this->prepareData($data));
        } else {
            $request->body($data)->attach($files);
        }

        try {
            $response = $request->send();
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), $e->getMessage());
        }

        return $this->createResponse(
            $this->parseProtocolVersion($response->raw_headers),
            $response->code,
            $this->parseReasonPhrase($response->raw_headers),
            $method,
            $response->headers->toArray(),
            $response->body,
            $url
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'httpful';
    }
}
