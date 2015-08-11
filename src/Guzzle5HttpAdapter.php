<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter;

/**
 * Guzzle 5 http adapter.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Guzzle5HttpAdapter extends Guzzle4HttpAdapter
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'guzzle5';
    }
}
