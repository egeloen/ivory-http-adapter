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

use Guzzle\Common\Version;
use Ivory\HttpAdapter\Guzzle3HttpAdapter;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Guzzle3HttpAdapterTest extends AbstractHttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!function_exists('curl_init')
            || !class_exists('Guzzle\Common\Version')
            || version_compare(Version::VERSION, '3.9.3', '<')
        ) {
            $this->markTestSkipped();
        }

        parent::setUp();
    }

    public function testGetName()
    {
        $this->assertSame('guzzle3', $this->httpAdapter->getName());
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapter()
    {
        return new Guzzle3HttpAdapter();
    }
}
