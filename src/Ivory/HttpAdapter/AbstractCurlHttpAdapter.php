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
use Ivory\HttpAdapter\Message\MessageInterface;

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
     * @param boolean checkExtension TRUE if the extension should be checked else FALSE.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the check extension is enabled and the curl extension is not loaded.
     */
    public function __construct($checkExtension = true)
    {
        if ($checkExtension && !function_exists('curl_init')) {
            throw HttpAdapterException::extensionIsNotLoaded('curl', $this->getName());
        }

        parent::__construct();
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
        if ($internalRequest->getProtocolVersion() === MessageInterface::PROTOCOL_VERSION_10) {
            return CURL_HTTP_VERSION_1_0;
        }

        return CURL_HTTP_VERSION_1_1;
    }

    /**
     * Prepares the data.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface $internalRequest The internal request.
     *
     * @return array|string The prepared data.
     */
    protected function prepareData(InternalRequestInterface $internalRequest)
    {
        if (!$internalRequest->hasFiles()) {
            return $this->prepareBody($internalRequest);
        }

        $files = array();

        foreach ($internalRequest->getFiles() as $key => $file) {
            $files[$key] = $this->isSafeUpload() ? new \CurlFile($file) : '@'.$file;
        }

        return array_merge($internalRequest->getData(), $files);
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
}
