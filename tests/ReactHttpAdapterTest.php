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
use Ivory\HttpAdapter\Message\Request;
use Ivory\HttpAdapter\ReactHttpAdapter;

/**
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

        // FIXME - https://github.com/reactphp/http-client/issues/11
        if (strpos($_SERVER['TEST_SERVER'], 'http://server') === 0) {
            $this->markTestSkipped();
        }

        $this->defaultOptions['protocol_version'] = Request::PROTOCOL_VERSION_1_0;
    }

    public function testGetName()
    {
        $this->assertSame('react', $this->httpAdapter->getName());
    }

    /**
     * @param float $timeout
     *
     * @dataProvider timeoutProvider
     */
    public function testSendWithTimeoutExceeded($timeout)
    {
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
        array $headers = [],
        array $data = [],
        array $files = [],
        $protocolVersion = MessageInterface::PROTOCOL_VERSION_1_0
    ) {
        parent::assertRequest($method, $headers, $data, $files, $protocolVersion);
    }
}
