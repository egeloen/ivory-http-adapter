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

use GuzzleHttp\Client;
use GuzzleHttp\Message\MessageFactory;
use Ivory\HttpAdapter\GuzzleHttpHttpAdapter;

/**
 * Abstract guzzle http http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractGuzzleHttpHttpAdapterTest extends AbstractHttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!class_exists('GuzzleHttp\Client')) {
            $this->markTestSkipped();
        }

        parent::setUp();
    }

    public function testGetName()
    {
        $this->assertSame('guzzle_http', $this->httpAdapter->getName());
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapter()
    {
        return new GuzzleHttpHttpAdapter(new Client(array('adapter' => $this->createAdapter())));
    }

    /**
     * Creates a guzzle4 adapter.
     *
     * @return \GuzzleHttp\Adapter\AdapterInterface The guzzle4 adapter.
     */
    abstract protected function createAdapter();

    /**
     * Creates a message factory.
     *
     * @return \GuzzleHttp\Message\MessageFactory The message factory.
     */
    protected function createMessageFactory()
    {
        return new MessageFactory();
    }
}
