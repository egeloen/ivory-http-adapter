<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Utils;

/**
 * PHPUnit utility.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PHPUnitUtility
{
    /**
     * Gets the url.
     *
     * @return string|boolean The url or FALSE if there is none.
     */
    public static function getUrl()
    {
        return isset($_SERVER['TEST_SERVER']) ? $_SERVER['TEST_SERVER'] : false;
    }
}
