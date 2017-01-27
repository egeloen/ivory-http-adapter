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

use Buzz\Browser;
use Buzz\Client\ClientInterface;
use Ivory\HttpAdapter\BuzzHttpAdapter;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractBuzzHttpAdapterTest extends AbstractHttpAdapterTest
{
    public function testGetName()
    {
        $this->assertSame('buzz', $this->httpAdapter->getName());
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapter()
    {
        return new BuzzHttpAdapter(new Browser($this->createClient()));
    }

    /**
     * @return ClientInterface
     */
    abstract protected function createClient();
}
