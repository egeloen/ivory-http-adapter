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
use Ivory\HttpAdapter\BuzzHttpAdapter;

/**
 * Abstract buzz http adapter test.
 *
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
     * Creates the buzz client.
     *
     * @return \Buzz\Client\ClientInterface The buzz client.
     */
    abstract protected function createClient();
}
