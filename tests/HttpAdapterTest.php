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

use Ivory\HttpAdapter\AbstractHttpAdapter;
use Ivory\HttpAdapter\ConfigurationInterface;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\MultiHttpAdapterException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class HttpAdapterTest extends AbstractTestCase
{
    /**
     * @var AbstractHttpAdapter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpAdapter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->httpAdapter = $this->createHttpAdapterMockBuilder()->getMockForAbstractClass();
    }

    public function testVersion()
    {
        $this->assertRegExp('/\d+\.\d+\.\d+(-(.?))?/', HttpAdapterInterface::VERSION);
    }

    public function testVersionId()
    {
        $this->assertRegExp('/\d+\d+\d+\d+\d+/', HttpAdapterInterface::VERSION_ID);
    }

    public function testMajorVersion()
    {
        $this->assertRegExp('/\d+/', HttpAdapterInterface::MAJOR_VERSION);
    }

    public function testMinorVersion()
    {
        $this->assertRegExp('/\d+/', HttpAdapterInterface::MINOR_VERSION);
    }

    public function testPatchVersion()
    {
        $this->assertRegExp('/\d+/', HttpAdapterInterface::PATCH_VERSION);
    }

    public function testExtraVersion()
    {
        $this->assertRegExp('/.?/', HttpAdapterInterface::EXTRA_VERSION);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Configuration', $this->httpAdapter->getConfiguration());
    }

    public function testInitialState()
    {
        $this->httpAdapter = $this->createHttpAdapterMockBuilder()
            ->setConstructorArgs([$configuration = $this->createConfigurationMock()])
            ->getMockForAbstractClass();

        $this->assertSame($configuration, $this->httpAdapter->getConfiguration());
    }

    public function testSetConfiguration()
    {
        $this->httpAdapter->setConfiguration($configuration = $this->createConfigurationMock());

        $this->assertSame($configuration, $this->httpAdapter->getConfiguration());
    }

    public function testSendRequestsWithInvalidRequests()
    {
        try {
            $this->httpAdapter->sendRequests([true]);
            $this->fail();
        } catch (MultiHttpAdapterException $e) {
            $this->assertFalse($e->hasResponses());

            $exceptions = $e->getExceptions();

            $this->assertCount(1, $exceptions);
            $this->assertArrayHasKey(0, $exceptions);
            $this->assertInstanceOf('Ivory\HttpAdapter\HttpAdapterException', $exceptions[0]);

            $this->assertSame(
                'The request must be a string, an array or implement "Psr\Http\Message\RequestInterface" ("boolean" given).',
                $exceptions[0]->getMessage()
            );

            $this->assertFalse($exceptions[0]->hasRequest());
            $this->assertFalse($exceptions[0]->hasResponse());
        }
    }

    /**
     * @return AbstractHttpAdapter|\PHPUnit_Framework_MockObject_MockBuilder
     */
    private function createHttpAdapterMockBuilder()
    {
        return $this->getMockBuilder('Ivory\HttpAdapter\AbstractHttpAdapter');
    }

    /**
     * @return ConfigurationInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createConfigurationMock()
    {
        return $this->createMock('Ivory\HttpAdapter\ConfigurationInterface');
    }
}
