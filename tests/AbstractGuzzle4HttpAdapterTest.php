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

use GuzzleHttp\Adapter\AdapterInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Message\MessageFactory;
use Ivory\HttpAdapter\Guzzle4HttpAdapter;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractGuzzle4HttpAdapterTest extends AbstractHttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!class_exists('GuzzleHttp\Adapter\Curl\CurlAdapter')) {
            $this->markTestSkipped();
        }

        parent::setUp();
    }

    public function testGetName()
    {
        $this->assertSame('guzzle4', $this->httpAdapter->getName());
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapter()
    {
        return new Guzzle4HttpAdapter(new Client(['adapter' => $this->createAdapter()]));
    }

    /**
     * @return AdapterInterface
     */
    abstract protected function createAdapter();

    /**
     * @return MessageFactory
     */
    protected function createMessageFactory()
    {
        return new MessageFactory();
    }
}
