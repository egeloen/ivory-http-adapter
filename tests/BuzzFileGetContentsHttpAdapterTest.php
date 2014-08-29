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

use Buzz\Client\FileGetContents;

/**
 * Buzz file get contents http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class BuzzFileGetContentsHttpAdapterTest extends AbstractBuzzHttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return new FileGetContents();
    }
}
