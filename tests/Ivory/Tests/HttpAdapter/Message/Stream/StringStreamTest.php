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

use Ivory\HttpAdapter\Message\Stream\StringStream;

/**
 * String stream test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class StringStreamTest extends AbstractStreamTest
{
    /**
     * {@inheritdoc}
     */
    protected function createStream($mode = null)
    {
        switch ($mode) {
            case self::MODE_SEEK_DISABLED:
                $mode = StringStream::MODE_READ | StringStream::MODE_WRITE;
                break;

            case self::MODE_READ_DISABLED:
                $mode = StringStream::MODE_SEEK | StringStream::MODE_WRITE;
                break;

            case self::MODE_WRITE_DISABLED:
                $mode = StringStream::MODE_SEEK | StringStream::MODE_READ;
                break;
        }

        return new StringStream($this->string, $mode);
    }
}
