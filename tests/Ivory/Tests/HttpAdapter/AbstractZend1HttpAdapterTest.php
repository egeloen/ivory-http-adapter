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

use Ivory\HttpAdapter\Zend1HttpAdapter;

/**
 * Abstract zend 1 http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractZend1HttpAdapterTest extends AbstractHttpAdapterTest
{
    public function testGetName()
    {
        $this->assertSame('zend1', $this->httpAdapter->getName());
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapter()
    {
        $client = new \Zend_Http_Client();
        $client->setAdapter($this->createAdapter());

        return new Zend1HttpAdapter($client);
    }

    /**
     * Creates a zend 1 adapter.
     *
     * @return \Zend_Http_Client_Adapter_Interface The zend 1 adapter.
     */
    abstract protected function createAdapter();
}
