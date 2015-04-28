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
    private $browser;

    /**
     * Creates a buzz http adapter.
     *
     * @param \Buzz\Browser|null                             $browser       The buzz browser.
     * @param \Ivory\HttpAdapter\ConfigurationInterface|null $configuration The configuration.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the browser client is multi curl.
     */
    public function __construct(Browser $browser = null, ConfigurationInterface $configuration = null)
    {
        $browser = $browser ?: new Browser();

        if ($browser->getClient() instanceof MultiCurl) {
            throw HttpAdapterException::doesNotSupportSubAdapter(
                $this->getName(),
                get_class($browser->getClient())
            );
        }

        parent::__construct($configuration, $browser->getClient() instanceof AbstractCurl);

        $this->browser = $browser;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'buzz';
    }

    /**
     * {@inheritdoc}
     */
    protected function sendInternalRequest(InternalRequestInterface $internalRequest)
    {
        $this->browser->getClient()->setTimeout($this->getConfiguration()->getTimeout());
        $this->browser->getClient()->setMaxRedirects(0);

        $request = $this->browser->getMessageFactory()->createRequest(
            $internalRequest->getMethod(),
            $uri = (string) $internalRequest->getUri()
        );

        $request->setProtocolVersion($internalRequest->getProtocolVersion());
        $request->setHeaders($this->prepareHeaders($internalRequest, false));

        $data = $this->browser->getClient() instanceof AbstractCurl
            ? $this->prepareContent($internalRequest)
            : $this->prepareBody($internalRequest);

        $request->setContent($data);

        try {
            $response = $this->browser->send($request);
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUri($uri, $this->getName(), $e->getMessage());
        }

        return $this->getConfiguration()->getMessageFactory()->createResponse(
            $response->getStatusCode(),
            sprintf('%.1f', $response->getProtocolVersion()),
            HeadersNormalizer::normalize($response->getHeaders()),
            BodyNormalizer::normalize($response->getContent(), $internalRequest->getMethod())
        );
    }
}
