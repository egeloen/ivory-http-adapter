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
use Ivory\HttpAdapter\Message\RequestInterface;

/**
 * Buzz stream http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class BuzzStreamHttpAdapterTest extends AbstractBuzzHttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return new FileGetContents();
    }
}
