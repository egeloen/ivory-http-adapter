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
 * Fopen http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class FopenHttpAdapter extends AbstractStreamHttpAdapter
{
    /**
     * {@inheritdoc}
     */
    protected function doSend($url, $method, array $headers = array(), $data = array(), array $files = array())
    {
        $context = $this->createContext($method, $headers, $data, $files);

        if (($resource = @fopen($this->prepareUrl($url), 'rb', false, $context)) === false) {
            throw HttpAdapterException::cannotFetchUrl($url, $this->getName(), print_r(error_get_last(), true));
        }

        return $this->createStreamResponse($url, $method, $http_response_header, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fopen';
    }
}
