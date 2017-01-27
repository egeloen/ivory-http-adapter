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

use GuzzleHttp\Handler\StreamHandler;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Guzzle6StreamHttpAdapterTest extends AbstractGuzzle6HttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function createHandler()
    {
        return new StreamHandler();
    }
}
