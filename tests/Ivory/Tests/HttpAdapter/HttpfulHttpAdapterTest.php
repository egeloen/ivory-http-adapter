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

use Ivory\HttpAdapter\HttpfulHttpAdapter;
use Ivory\HttpAdapter\Message\RequestInterface;

/**
 * Description of HttpfulHttpAdapterTest
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpfulHttpAdapterTest extends AbstractHttpAdapterTest
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

    public function testGetName()
    {
        $this->assertSame('httpful', $this->httpAdapter->getName());
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapter()
    {
        return new HttpfulHttpAdapter();
    }
}
