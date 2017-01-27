<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Utility;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PHPUnitUtility
{
    /**
     * @return string|bool
     */
    public static function getUri()
    {
        return isset($_SERVER['TEST_SERVER']) ? $_SERVER['TEST_SERVER'] : false;
    }

    /**
     * @param bool        $tmp
     * @param string|null $name
     *
     * @return string
     */
    public static function getFile($tmp = true, $name = null)
    {
        return ($tmp ? realpath(sys_get_temp_dir()) : '').'/'.($name === null ? uniqid() : $name);
    }
}
