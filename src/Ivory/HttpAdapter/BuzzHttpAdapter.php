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

use Buzz\Browser;
use Buzz\Client\AbstractCurl;
use Buzz\Client\MultiCurl;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Normalizer\BodyNormalizer;
use Ivory\HttpAdapter\Normalizer\HeadersNormalizer;

/**
 * Buzz http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class BuzzHttpAdapter extends AbstractCurlHttpAdapter
{
    /** @var \Buzz\Browser */
    protected $browser;

    /**
     * Creates a buzz http adapter.
     *
     * @param \Buzz\Browser $browser The buzz browser.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the browser client is multi curl.
     */
    public function __construct(Browser $browser = null)
    {
        $browser = $browser ?: new Browser();

        if ($browser->getClient() instanceof MultiCurl) {
            throw HttpAdapterException::doesNotSupportSubAdapter(
                $this->getName(),
                get_class($browser->getClient())
            );
        }

        parent::__construct($browser->getClient() instanceof AbstractCurl);

        $this->browser = $browser;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSend(InternalRequestInterface $internalRequest)
    {
        $this->browser->getClient()->setTimeout($this->timeout);
        $this->browser->getClient()->setMaxRedirects($this->maxRedirects);
        $this->browser->getClient()->setIgnoreErrors(!$this->hasMaxRedirects() && PHP_VERSION_ID === 50303);

        $request = $this->browser->getMessageFactory()->createRequest(
            $internalRequest->getMethod(),
            $internalRequest->getUrl()
        );

        $request->setProtocolVersion($internalRequest->getProtocolVersion());
        $request->setHeaders($this->prepareHeaders($internalRequest, false));

        $data = $this->browser->getClient() instanceof AbstractCurl
            ? $this->prepareData($internalRequest)
            : $this->prepareBody($internalRequest);

        $request->setContent($data);

        try {
            $response = $this->browser->send($request);
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUrl($internalRequest->getUrl(), $this->getName(), $e->getMessage());
        }

        return $this->createResponse(
            (string) $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            HeadersNormalizer::normalize($response->getHeaders()),
            BodyNormalizer::normalize($response->getContent(), $internalRequest->getMethod()),
            $internalRequest->getUrl()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'buzz';
    }
}
