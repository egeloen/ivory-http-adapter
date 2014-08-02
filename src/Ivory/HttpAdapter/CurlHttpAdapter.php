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

use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Ivory\HttpAdapter\Message\RequestInterface;

/**
 * Curl http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CurlHttpAdapter extends AbstractCurlHttpAdapter
{
    /**
     * {@inheritdoc}
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the curl extension is not loaded.
     */
    public function __construct(MessageFactoryInterface $messageFactory = null)
    {
        if (!function_exists('curl_init')) {
            throw HttpAdapterException::extensionIsNotLoaded('curl', $this->getName());
        }

        parent::__construct($messageFactory);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSend($url, $method, array $headers = array(), $data = array(), array $files = array())
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->prepareUrl($url));
        curl_setopt($curl, CURLOPT_HTTP_VERSION, $this->prepareProtocolVersion($this->protocolVersion));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->prepareHeaders($headers, array(), array(), false));
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $this->hasMaxRedirects());

        if ($this->hasMaxRedirects()) {
            curl_setopt($curl, CURLOPT_MAXREDIRS, $this->maxRedirects);
        }

        if (!empty($files) && $this->isSafeUpload()) {
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
        }

        switch ($this->prepareMethod($method)) {
            case RequestInterface::METHOD_HEAD:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($curl, CURLOPT_NOBODY, true);
                break;

            case RequestInterface::METHOD_POST:
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $this->prepareFiles($data, $files));
                break;

            case RequestInterface::METHOD_PUT:
            case RequestInterface::METHOD_PATCH:
            case RequestInterface::METHOD_DELETE:
            case RequestInterface::METHOD_OPTIONS:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $this->prepareFiles($data, $files));
                break;
        }

        if (($response = curl_exec($curl)) === false) {
            $error = curl_error($curl);
            curl_close($curl);

            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), $error);
        }

        $headersSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $effectiveUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

        curl_close($curl);

        $headers = substr($response, 0, $headersSize);
        $body = substr($response, $headersSize);

        return $this->createResponse(
            $this->parseProtocolVersion($headers),
            $this->parseStatusCode($headers),
            $this->parseReasonPhrase($headers),
            $method,
            $headers,
            $body,
            $effectiveUrl
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'curl';
    }
}
