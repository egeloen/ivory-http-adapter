<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter;

use GuzzleHttp\Adapter\StreamAdapter;
use GuzzleHttp\Ring\Client\StreamHandler;

/**
 * Guzzle http stream http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class GuzzleHttpStreamHttpAdapterTest extends AbstractGuzzleHttpHttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function createAdapter()
    {
        if (class_exists('GuzzleHttp\Ring\Client\StreamHandler')) {
            return new StreamHandler();
        }

        return new StreamAdapter($this->createMessageFactory());
    }
}
