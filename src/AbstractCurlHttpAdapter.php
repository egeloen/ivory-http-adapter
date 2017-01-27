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

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractCurlHttpAdapter extends AbstractHttpAdapter
{
    /**
     * @param ConfigurationInterface|null $configuration
     * @param bool                        $checkExtension
     *
     * @throws HttpAdapterException
     */
    public function __construct(ConfigurationInterface $configuration = null, $checkExtension = true)
    {
        if ($checkExtension && !function_exists('curl_init')) {
            throw HttpAdapterException::extensionIsNotLoaded('curl', $this->getName());
        }

        parent::__construct($configuration);
    }

    /**
     * @param InternalRequestInterface $internalRequest
     *
     * @return int
     */
    protected function prepareProtocolVersion(InternalRequestInterface $internalRequest)
    {
        return $internalRequest->getProtocolVersion() === InternalRequestInterface::PROTOCOL_VERSION_1_0
            ? CURL_HTTP_VERSION_1_0
            : CURL_HTTP_VERSION_1_1;
    }

    /**
     * @param InternalRequestInterface $internalRequest
     *
     * @return array|string
     */
    protected function prepareContent(InternalRequestInterface $internalRequest)
    {
        $files = $internalRequest->getFiles();

        if (empty($files)) {
            return $this->prepareBody($internalRequest);
        }

        $content = [];

        foreach ($internalRequest->getDatas() as $name => $data) {
            $content = array_merge($content, $this->prepareRawContent($name, $data));
        }

        foreach ($files as $name => $file) {
            $content = array_merge($content, $this->prepareRawContent($name, $file, true));
        }

        return $content;
    }

    /**
     * @param string $file
     *
     * @return mixed
     */
    protected function createFile($file)
    {
        return $this->isSafeUpload() ? new \CurlFile($file) : '@'.$file;
    }

    /**
     * @return bool
     */
    protected function isSafeUpload()
    {
        return defined('CURLOPT_SAFE_UPLOAD');
    }

    /**
     * @param string       $name
     * @param array|string $data
     * @param bool         $isFile
     *
     * @return array
     */
    private function prepareRawContent($name, $data, $isFile = false)
    {
        if (is_array($data)) {
            $preparedData = [];

            foreach ($data as $subName => $subData) {
                $preparedData = array_merge(
                    $preparedData,
                    $this->prepareRawContent($this->prepareName($name, $subName), $subData, $isFile)
                );
            }

            return $preparedData;
        }

        return [$name => $isFile ? $this->createFile($data) : $data];
    }
}
