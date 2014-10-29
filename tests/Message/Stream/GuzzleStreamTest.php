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
use Ivory\HttpAdapter\Message\Stream\GuzzleStream;

/**
 * Guzzle stream test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class GuzzleStreamTest extends AbstractResourceStreamTest
{
    /**
     * {@inheritdoc}
     */
    protected function createStream($string, $mode = null)
    {
        return new GuzzleStream($this->createSubStream($string, $mode));
    }

    /**
     * {@inheritdoc}
     */
    protected function createSubStream($string, $mode = null)
    {
        return new Stream(parent::createSubStream($string, $mode));
    }
}
