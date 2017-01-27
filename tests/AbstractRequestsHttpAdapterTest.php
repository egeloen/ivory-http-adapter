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

use Ivory\HttpAdapter\RequestsHttpAdapter;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractRequestsHttpAdapterTest extends AbstractHttpAdapterTest
{
    public function testGetName()
    {
        $this->assertSame('requests', $this->httpAdapter->getName());
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapter()
    {
        return new RequestsHttpAdapter($this->createTransport());
    }

    /**
     * @return \Requests_Transport
     */
    abstract protected function createTransport();
}
