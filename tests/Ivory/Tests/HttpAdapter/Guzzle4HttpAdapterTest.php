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

use Ivory\HttpAdapter\Guzzle4HttpAdapter;

/**
 * Guzzle 4 http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Guzzle4HttpAdapterTest extends AbstractHttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!function_exists('curl_init') || !class_exists('GuzzleHttp\Client')) {
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
        return new Guzzle4HttpAdapter();
    }
}
