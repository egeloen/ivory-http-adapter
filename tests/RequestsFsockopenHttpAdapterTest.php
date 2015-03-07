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

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RequestsFsockopenHttpAdapterTest extends AbstractRequestsHttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function createTransport()
    {
        return new \Requests_Transport_fsockopen();
    }
}
