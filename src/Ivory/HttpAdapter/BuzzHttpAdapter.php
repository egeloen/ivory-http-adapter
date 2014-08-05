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
use Buzz\Client\AbstractStream;
use Buzz\Client\Curl;
use Buzz\Client\MultiCurl;
use Ivory\HttpAdapter\Message\MessageFactoryInterface;

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
     * @param \Buzz\Browser                                           $browser        The buzz browser.
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface|null $messageFactory The message factory.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the browser client is multi curl.
     */
    public function __construct(Browser $browser = null, MessageFactoryInterface $messageFactory = null)
    {
        if ($browser !== null) {
            if ($browser->getClient() instanceof MultiCurl) {
                throw HttpAdapterException::doesNotSupportSubAdapter(
                    $this->getName(),
                    get_class($browser->getClient())
                );
            }

            if ($browser->getClient() instanceof Curl) {
                parent::__construct($messageFactory);
            } else {
                parent::__construct($messageFactory, false);
            }
        }

        $this->browser = $browser ?: new Browser();
    }

    /**
     * {@inheritdoc}
     */
    protected function doSend($url, $method, array $headers = array(), $data = array(), array $files = array())
    {
        $this->browser->getClient()->setIgnoreErrors(!$this->hasMaxRedirects() && PHP_VERSION_ID === 50303);
        $this->browser->getClient()->setMaxRedirects($this->maxRedirects);

        $request = $this->browser->getMessageFactory()->createRequest(
            $this->prepareMethod($method),
            $url = $this->prepareUrl($url)
        );

        $request->setProtocolVersion($this->protocolVersion);
        $request->setHeaders($this->prepareHeaders($headers, $data, $files, false));

        if ($this->browser->getClient() instanceof AbstractCurl) {
            $data = $this->prepareFiles($data, $files);
        } elseif ($this->browser->getClient() instanceof AbstractStream) {
            $data = $this->prepareData($data, $files);
        }

        $request->setContent($data);

        try {
            $response = $this->browser->send($request);
        } catch (\Exception $e) {
            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), $e->getMessage());
        }

        return $this->createResponse(
            (string) $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $method,
            $response->getHeaders(),
            $response->getContent(),
            $url
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
