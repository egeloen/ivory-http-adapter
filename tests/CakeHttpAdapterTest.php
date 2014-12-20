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

use Ivory\HttpAdapter\CakeHttpAdapter;

/**
 * Cake http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CakeHttpAdapterTest extends AbstractHttpAdapterTest
{
    /**
     * {@inheritdoc
     */
    protected function setUp()
    {
        if (!defined('APP_DIR')) {
            define('APP_DIR', 'app');
        }

        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }

        if (!defined('ROOT')) {
            define('ROOT', realpath(__DIR__.'/../vendor/cakephp/cakephp'));
        }

        if (!defined('WWW_ROOT')) {
            define('WWW_ROOT', ROOT.DS.APP_DIR.DS.'webroot'.DS);
        }

        require_once __DIR__.'/../vendor/cakephp/cakephp/lib/Cake/bootstrap.php';
        \App::uses('HttpSocket', 'Network/Http');

        parent::setUp();
    }

    public function testGetName()
    {
        $this->assertSame('cake', $this->httpAdapter->getName());
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapter()
    {
        return new CakeHttpAdapter();
    }
}
