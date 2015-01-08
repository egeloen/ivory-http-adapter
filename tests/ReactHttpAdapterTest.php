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

use Ivory\HttpAdapter\Message\MessageInterface;
use Ivory\HttpAdapter\ReactHttpAdapter;

/**
 * React http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ReactHttpAdapterTest extends AbstractHttpAdapterTest
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!class_exists('React\HttpClient\Factory')) {
            $this->markTestSkipped();
        }

        parent::setUp();
    }

    public function testGetName()
    {
        $this->assertSame('react', $this->httpAdapter->getName());
    }

    /**
     * @dataProvider timeoutProvider
     */
    public function testSendWithTimeoutExceeded($timeout)
    {
        // ReactPHP does not allow to control timeout
        // https://github.com/reactphp/socket-client/pull/17
        $this->markTestSkipped();
    }

    public function testSendWithSelfSignedSslCertificate()
    {
        // React does not allow to disable SSL verification
        $this->markTestSkipped();
    }

    public function testSendWithInvalidSslCertificate()
    {
        // React does not allow to disable SSL verification
        $this->markTestSkipped();
    }

    /**
     * {@inheritdoc}
     */
    protected function createHttpAdapter()
    {
        return new ReactHttpAdapter();
    }

    /**
     * {@inheritdoc}
     */
    protected function assertRequest(
        $method,
        array $headers = array(),
        array $data = array(),
        array $files = array(),
        $protocolVersion = MessageInterface::PROTOCOL_VERSION_1_0
    ) {
        // ReactPHP is only compatible with the HTTP 1.0 protocol version.
        parent::assertRequest($method, $headers, $data, $files, $protocolVersion);
    }
}
