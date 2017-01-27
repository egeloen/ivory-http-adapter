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
use Ivory\HttpAdapter\Guzzle6HttpAdapter;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractGuzzle6HttpAdapterTest extends AbstractHttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!class_exists('GuzzleHttp\Handler\CurlHandler')) {
            $this->markTestSkipped();
        }

        parent::setUp();
    }

    public function testGetName()
    {
        $this->assertSame('guzzle6', $this->httpAdapter->getName());
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapter()
    {
        return new Guzzle6HttpAdapter(new Client(['handler' => $this->createHandler()]));
    }

    /**
     * @return object
     */
    abstract protected function createHandler();
}
