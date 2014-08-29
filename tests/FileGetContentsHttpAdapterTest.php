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

use Ivory\HttpAdapter\FileGetContentsHttpAdapter;

/**
 * File get contents http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class FileGetContentsHttpAdapterTest extends AbstractHttpAdapterTest
{
    public function testGetName()
    {
        $this->assertSame('file_get_contents', $this->httpAdapter->getName());
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapter()
    {
        return new FileGetContentsHttpAdapter();
    }
}
