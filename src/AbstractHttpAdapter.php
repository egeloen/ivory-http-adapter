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
use Ivory\HttpAdapter\Normalizer\HeadersNormalizer;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractHttpAdapter implements HttpAdapterInterface
{
    use HttpAdapterTrait;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface|null $configuration
     */
    public function __construct(ConfigurationInterface $configuration = null)
    {
        $this->setConfiguration($configuration ?: new Configuration());
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param InternalRequestInterface $internalRequest
     * @param bool                     $associative
     * @param bool                     $contentType
     * @param bool                     $contentLength
     *
     * @return array the prepared headers
     */
    protected function prepareHeaders(
        InternalRequestInterface &$internalRequest,
        $associative = true,
        $contentType = true,
        $contentLength = false
    ) {
        if (!$internalRequest->hasHeader('Connection')) {
            $internalRequest = $internalRequest->withHeader(
                'Connection',
                $this->configuration->getKeepAlive() ? 'keep-alive' : 'close'
            );
        }

        if (!$internalRequest->hasHeader('Content-Type')) {
            $rawDatas = (string) $internalRequest->getBody();
            $datas = $internalRequest->getDatas();
            $files = $internalRequest->getFiles();

            if ($this->configuration->hasEncodingType()) {
                $internalRequest = $internalRequest->withHeader(
                    'Content-Type',
                    $this->configuration->getEncodingType()
                );
            } elseif ($contentType && !empty($files)) {
                $internalRequest = $internalRequest->withHeader(
                    'Content-Type',
                    ConfigurationInterface::ENCODING_TYPE_FORMDATA.'; boundary='.$this->configuration->getBoundary()
                );
            } elseif ($contentType && (!empty($datas) || !empty($rawDatas))) {
                $internalRequest = $internalRequest->withHeader(
                    'Content-Type',
                    ConfigurationInterface::ENCODING_TYPE_URLENCODED
                );
            }
        }

        if ($contentLength && !$internalRequest->hasHeader('Content-Length')
            && ($length = strlen($this->prepareBody($internalRequest))) > 0) {
            $internalRequest = $internalRequest->withHeader('Content-Length', (string) $length);
        }

        if (!$internalRequest->hasHeader('User-Agent')) {
            $internalRequest = $internalRequest->withHeader('User-Agent', $this->configuration->getUserAgent());
        }

        return HeadersNormalizer::normalize($internalRequest->getHeaders(), $associative);
    }

    /**
     * @param InternalRequestInterface $internalRequest
     *
     * @return string
     */
    protected function prepareBody(InternalRequestInterface $internalRequest)
    {
        $body = (string) $internalRequest->getBody();

        if (!empty($body)) {
            return $body;
        }

        $files = $internalRequest->getFiles();

        if (empty($files)) {
            return http_build_query($internalRequest->getDatas(), null, '&');
        }

        $body = '';

        foreach ($internalRequest->getDatas() as $name => $value) {
            $body .= $this->prepareRawBody($name, $value);
        }

        foreach ($internalRequest->getFiles() as $name => $file) {
            $body .= $this->prepareRawBody($name, $file, true);
        }

        $body .= '--'.$this->configuration->getBoundary().'--'."\r\n";

        return $body;
    }

    /**
     * @param string $name
     * @param string $subName
     *
     * @return string
     */
    protected function prepareName($name, $subName)
    {
        return $name.'['.$subName.']';
    }

    /**
     * @param string       $name
     * @param array|string $data
     * @param bool         $isFile
     *
     * @return string
     */
    private function prepareRawBody($name, $data, $isFile = false)
    {
        if (is_array($data)) {
            $body = '';

            foreach ($data as $subName => $subData) {
                $body .= $this->prepareRawBody($this->prepareName($name, $subName), $subData, $isFile);
            }

            return $body;
        }

        $body = '--'.$this->configuration->getBoundary()."\r\n".'Content-Disposition: form-data; name="'.$name.'"';

        if ($isFile) {
            $body .= '; filename="'.basename($data).'"';
            $data = file_get_contents($data);
        }

        return $body."\r\n\r\n".$data."\r\n";
    }
}
