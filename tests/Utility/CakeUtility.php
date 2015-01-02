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
 * Cake utility.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CakeUtility
{
    /**
     * Sets up cacke.
     */
    public static function setUp()
    {
        if (class_exists('HttpSocket')) {
            return;
        }

        if (!defined('APP_DIR')) {
            define('APP_DIR', 'app');
        }

        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }

        if (!defined('ROOT')) {
            define('ROOT', realpath(__DIR__.DS.'..'.DS.'..'.DS.'vendor'.DS.'cakephp'.DS.'cakephp'));
        }

        if (!defined('WWW_ROOT')) {
            define('WWW_ROOT', ROOT.DS.APP_DIR.DS.'webroot'.DS);
        }

        require_once ROOT.DS.'lib'.DS.'Cake'.DS.'bootstrap.php';

        \App::uses('HttpSocket', 'Network/Http');
    }
}
