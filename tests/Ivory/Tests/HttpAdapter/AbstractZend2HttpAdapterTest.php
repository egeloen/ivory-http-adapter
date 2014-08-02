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
use Ivory\HttpAdapter\Zend2HttpAdapter;
use Zend\Http\Client;

/**
 * Abstract zend 2 http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractZend2HttpAdapterTest extends AbstractHttpAdapterTest
{
    public function testSendWithSingleRedirect()
    {
        $this->assertResponse(
            $this->httpAdapter->send($redirectUrl = $this->getRedirectUrl(), $method = RequestInterface::METHOD_GET),
            array('effective_url' => $redirectUrl)
        );

        $this->assertRequest($method);
    }

    public function testSendWithMultipleRedirects()
    {
        $this->assertResponse(
            $this->httpAdapter->send(
                $redirectUrl = $this->getRedirectUrl($this->httpAdapter->getMaxRedirects()),
                $method = RequestInterface::METHOD_GET
            ),
            array('effective_url' => $redirectUrl)
        );

        $this->assertRequest($method);
    }

    /**
     * @dataProvider fullProvider
     *
     * FIXME - https://github.com/zendframework/zf2/pull/6492
     */
    public function testOptions($url, array $headers = array(), array $data = array(), array $files = array())
    {
        $data = array();
        $files = array();

        parent::testOptions($url, $headers, $data, $files);
    }

    /**
     * @dataProvider requestProvider
     *
     * FIXME - https://github.com/zendframework/zf2/pull/6492
     */
    public function testSendRequest($url, $method, array $headers = array(), array $data = array())
    {
        if ($method === RequestInterface::METHOD_OPTIONS) {
            $data = array();
        }

        parent::testSendRequest($url, $method, $headers, $data);
    }

    public function testGetName()
    {
        $this->assertSame('zend2', $this->httpAdapter->getName());
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapter()
    {
        $client = new Client();
        $client->setAdapter($this->createAdapter());

        return new Zend2HttpAdapter($client);
    }

    /**
     * Creates a zend 2 adapter.
     *
     * @return \Zend\Http\Client\Adapter\AdapterInterface The zend 2 adapter.
     */
    abstract protected function createAdapter();
}
