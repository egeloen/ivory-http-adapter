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
 * Abstract curl http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractCurlHttpAdapter extends AbstractHttpAdapter
{
    /**
     * Creates a curl http adapter.
     *
     * @param \Ivory\HttpAdapter\ConfigurationInterface|null $configuration  The configuration.
     * @param boolean                                        $checkExtension TRUE if the extension should be checked else FALSE.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the check extension is enabled and the curl extension is not loaded.
     */
    public function __construct(ConfigurationInterface $configuration = null, $checkExtension = true)
    {
        if ($checkExtension && !function_exists('curl_init')) {
            throw HttpAdapterException::extensionIsNotLoaded('curl', $this->getName());
        }

        parent::__construct($configuration);
    }

    /**
     * Prepares the protocol version.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @return integer The prepared protocol version.
     */
    protected function prepareProtocolVersion(InternalRequestInterface $internalRequest)
    {
        return $internalRequest->getProtocolVersion() === InternalRequestInterface::PROTOCOL_VERSION_1_0
            ? CURL_HTTP_VERSION_1_0
            : CURL_HTTP_VERSION_1_1;
    }

    /**
     * Prepares the content.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @return array|string The prepared content.
     */
    protected function prepareContent(InternalRequestInterface $internalRequest)
    {
        $files = $internalRequest->getFiles();

        if (empty($files)) {
            return $this->prepareBody($internalRequest);
        }

        $content = array();

        foreach ($internalRequest->getDatas() as $name => $data) {
            $content = array_merge($content, $this->prepareRawContent($name, $data));
        }

        foreach ($files as $name => $file) {
            $content = array_merge($content, $this->prepareRawContent($name, $file, true));
        }

        return $content;
    }

    /**
     * Creates a file.
     *
     * @param string $file The file.
     *
     * @return mixed The created file.
     */
    protected function createFile($file)
    {
        return $this->isSafeUpload() ? new \CurlFile($file) : '@'.$file;
    }

    /**
     * Checks if it is safe upload.
     *
     * @return boolean TRUE if it is safe upload else FALSE.
     */
    protected function isSafeUpload()
    {
        return defined('CURLOPT_SAFE_UPLOAD');
    }

    /**
     * Prepares the raw content.
     *
     * @param string       $name   The name.
     * @param array|string $data   The data.
     * @param boolean      $isFile TRUE if the data is a file path else FALSE.
     *
     * @return array The prepared raw content.
     */
    private function prepareRawContent($name, $data, $isFile = false)
    {
        if (is_array($data)) {
            $preparedData = array();

            foreach ($data as $subName => $subData) {
                $preparedData = array_merge(
                    $preparedData,
                    $this->prepareRawContent($this->prepareName($name, $subName), $subData, $isFile)
                );
            }

            return $preparedData;
        }

        return array($name => $isFile ? $this->createFile($data) : $data);
    }
}
