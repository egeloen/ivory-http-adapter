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
use Ivory\HttpAdapter\Guzzle5HttpAdapter;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractGuzzle5HttpAdapterTest extends AbstractHttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!class_exists('GuzzleHttp\Ring\Client\CurlHandler')) {
            $this->markTestSkipped();
        }

        parent::setUp();
    }

    public function testGetName()
    {
        $this->assertSame('guzzle5', $this->httpAdapter->getName());
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapter()
    {
        return new Guzzle5HttpAdapter(new Client(['handler' => $this->createHandler()]));
    }

    /**
     * @return object
     */
    abstract protected function createHandler();
}
