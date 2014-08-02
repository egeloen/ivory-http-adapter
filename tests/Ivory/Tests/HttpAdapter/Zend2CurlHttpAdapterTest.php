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

use Ivory\HttpAdapter\Message\RequestInterface;
use Zend\Http\Client\Adapter\Curl;

/**
 * Zend 2 curl http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Zend2CurlHttpAdapterTest extends AbstractZend2HttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!function_exists('curl_init')) {
            $this->markTestSkipped();
        }

        parent::setUp();
    }

    /**
     * @dataProvider fullProvider
     *
     * FIXME - https://github.com/zendframework/zf2/pull/6492
     */
    public function testDelete($url, array $headers = array(), array $data = array(), array $files = array())
    {
        $data = array();
        $files = array();

        parent::testDelete($url, $headers, $data, $files);
    }

    /**
     * @dataProvider requestProvider
     *
     * FIXME - https://github.com/zendframework/zf2/pull/6492
     */
    public function testSendRequest($url, $method, array $headers = array(), array $data = array())
    {
        if ($method === RequestInterface::METHOD_DELETE) {
            $data = array();
        }

        parent::testSendRequest($url, $method, $headers, $data);
    }

    /**
     * {@inheritdoc}
     *
     * FIXME - https://github.com/zendframework/zf2/pull/6485
     */
    protected function assertRequest(
        $method,
        array $headers = array(),
        array $data = array(),
        array $files = array(),
        $protocolVersion = RequestInterface::PROTOCOL_VERSION_11
    ) {
        $protocolVersion = RequestInterface::PROTOCOL_VERSION_11;

        parent::assertRequest($method, $headers, $data, $files, $protocolVersion);
    }

    /**
     * {@inheritdoc}
     */
    protected function createAdapter()
    {
        return new Curl();
    }
}
