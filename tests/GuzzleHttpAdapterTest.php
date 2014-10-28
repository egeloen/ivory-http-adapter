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

use Ivory\HttpAdapter\GuzzleHttpAdapter;

/**
 * Guzzle http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class GuzzleHttpAdapterTest extends AbstractHttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!function_exists('curl_init')) {
            $this->markTestSkipped();
        }

        parent::setUp();
    }

    public function testGetName()
    {
        $this->assertSame('guzzle', $this->httpAdapter->getName());
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapter()
    {
        return new GuzzleHttpAdapter();
    }
}
