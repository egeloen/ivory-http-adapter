<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event;

use Ivory\HttpAdapter\Event\PreSendEvent;

/**
 * Pre send event test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PreSendEventTest extends AbstractEventTest
{
    /**
     * {@inheritdoc}
     */
    protected function createEvent()
    {
        return new PreSendEvent($this->httpAdapter, $this->request);
    }
}
