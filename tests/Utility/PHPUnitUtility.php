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

    /**
     * Gets the file.
     *
     * @param boolean     $tmp  TRUE if the file should be in the "/tmp" directory else FALSE.
     * @param string|null $name The name.
     *
     * @return string The file.
     */
    public static function getFile($tmp = true, $name = null)
    {
        return ($tmp ? realpath(sys_get_temp_dir()) : '').'/'.($name === null ? uniqid() : $name);
    }
}
