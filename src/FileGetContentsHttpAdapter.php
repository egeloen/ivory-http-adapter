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
    public function getName()
    {
        return 'file_get_contents';
    }

    /**
     * {@inheritdoc}
     */
    protected function process($uri, $context)
    {
        $http_response_header = array();

        return array(@file_get_contents($uri, false, $context), $http_response_header);
    }
}
