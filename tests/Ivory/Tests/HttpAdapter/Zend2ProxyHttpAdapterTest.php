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

use Zend\Http\Client\Adapter\Proxy;

/**
 * Zend 2 proxy http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Zend2ProxyHttpAdapterTest extends AbstractZend2HttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function createAdapter()
    {
        return new Proxy();
    }
}
