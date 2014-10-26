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

/**
 * Abstract resource stream test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractResourceStreamTest extends AbstractStreamTest
{
    /** @var resource */
    protected $resource;

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->closeResource();
        unset($this->resource);

        parent::tearDown();
    }

    /**
     * {@inheritdoc}
     */
    protected function assertSubStream($subStream)
    {
        $this->assertSame($this->resource, $subStream);
    }

    /**
     * {@inheritdoc}
     */
    protected function createSubStream($string, $mode = null)
    {
        $this->closeResource();

        $path = realpath(__DIR__.'/../../Fixtures/files/resource.txt');
        file_put_contents($path, $string);

        switch ($mode) {
            case self::MODE_SEEK_DISABLED:
                $path = 'php://output';
                $mode = 'r';
                break;

            case self::MODE_READ_DISABLED:
                $mode = 'a';
                break;

            case self::MODE_WRITE_DISABLED:
                $mode = 'r';
                break;

            default:
                $mode = 'r+';
                break;
        }

        return $this->resource = fopen($path, $mode);
    }

    /**
     * Closes the resource.
     */
    protected function closeResource()
    {
        if (is_resource($this->resource)) {
            fclose($this->resource);
        }
    }
}
