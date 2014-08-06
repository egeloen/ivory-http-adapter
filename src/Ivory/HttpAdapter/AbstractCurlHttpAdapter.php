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
     * @param string $protocolVersion The protocol version.
     *
     * @return integer The prepared protocol version.
     */
    protected function prepareProtocolVersion($protocolVersion)
    {
        if ($protocolVersion === MessageInterface::PROTOCOL_VERSION_10) {
            return CURL_HTTP_VERSION_1_0;
        }

        return CURL_HTTP_VERSION_1_1;
    }

    /**
     * Prepares the files.
     *
     * @param array|string $data  The data.
     * @param array        $files The files.
     *
     * @return array|string The prepared files.
     */
    protected function prepareFiles($data, array $files)
    {
        if (empty($files)) {
            return $this->prepareData($data);
        }

        foreach ($files as $key => $file) {
            $files[$key] = $this->isSafeUpload() ? new \CurlFile($file) : '@'.$file;
        }

        return array_merge($data, $files);
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
