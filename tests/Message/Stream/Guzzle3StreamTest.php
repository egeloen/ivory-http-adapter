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

use Guzzle\Stream\Stream;
use Ivory\HttpAdapter\Message\Stream\Guzzle3Stream;

/**
 * Guzzle 3 stream test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Guzzle3StreamTest extends AbstractResourceStreamTest
{
    /**
     * {@inheritdoc}
     */
    protected function createStream($mode = null)
    {
        return new Guzzle3Stream(new Stream($this->createResource($mode)));
    }
}
