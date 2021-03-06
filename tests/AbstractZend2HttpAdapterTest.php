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

use Ivory\HttpAdapter\Zend2HttpAdapter;
use Zend\Http\Client;
use Zend\Http\Client\Adapter\AdapterInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractZend2HttpAdapterTest extends AbstractHttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!class_exists('Zend\Http\Client')) {
            $this->markTestSkipped();
        }

        parent::setUp();
    }

    public function testGetName()
    {
        $this->assertSame('zend2', $this->httpAdapter->getName());
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapter()
    {
        $client = new Client();
        $client->setAdapter($this->createAdapter());

        return new Zend2HttpAdapter($client);
    }

    /**
     * @return AdapterInterface
     */
    abstract protected function createAdapter();
}
