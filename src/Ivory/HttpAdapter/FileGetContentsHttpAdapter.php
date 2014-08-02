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

/**
 * File get contents http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class FileGetContentsHttpAdapter extends AbstractStreamHttpAdapter
{
    /**
     * {@inheritdoc}
     */
    protected function doSend($url, $method, array $headers = array(), $data = array(), array $files = array())
    {
        $context = $this->createContext($method, $headers, $data, $files);

        if (($body = @file_get_contents($this->prepareUrl($url), false, $context)) === false) {
            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), print_r(error_get_last(), true));
        }

        return $this->createStreamResponse($url, $method, $http_response_header, $body);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'file_get_contents';
    }
}
