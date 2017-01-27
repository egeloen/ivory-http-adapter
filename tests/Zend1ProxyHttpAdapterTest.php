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

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Zend1ProxyHttpAdapterTest extends AbstractZend1HttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function createAdapter()
    {
        return new \Zend_Http_Client_Adapter_Proxy();
    }
}
