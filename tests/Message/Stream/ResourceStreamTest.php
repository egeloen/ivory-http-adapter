<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Message\Stream;

use Ivory\HttpAdapter\Message\Stream\ResourceStream;
use Ivory\Tests\HttpAdapter\Utility\PHPUnitUtility;

/**
 * Resource stream test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceStreamTest extends AbstractResourceStreamTest
{
    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testInvalidResource()
    {
        new ResourceStream('foo');
    }

    public function testGetSizeWithRemoteStream()
    {
        $url = PHPUnitUtility::getUrl();

        if (!$url) {
            $this->markTestSkipped();
        }

        $this->stream = new ResourceStream($this->resource = fopen($url, 'rb', false));

        $this->assertSame(2, $this->stream->getSize());
    }

    /**
     * {@inheritdoc}
     */
    protected function createStream($string, $mode = null)
    {
        return new ResourceStream($this->createSubStream($string, $mode));
    }
}
