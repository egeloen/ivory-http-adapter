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

use GuzzleHttp\Stream\Stream;
use Ivory\HttpAdapter\Message\Stream\Guzzle4Stream;

/**
 * Guzzle 4 stream test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Guzzle4StreamTest extends AbstractResourceStreamTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!class_exists('GuzzleHttp\Stream\Stream')) {
            $this->markTestSkipped();
        }

        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function createStream($string, $mode = null)
    {
        return new Guzzle4Stream($this->createSubStream($string, $mode));
    }

    /**
     * {@inheritdoc}
     */
    protected function createSubStream($string, $mode = null)
    {
        return new Stream(parent::createSubStream($string, $mode));
    }
}
