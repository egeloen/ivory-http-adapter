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

use Ivory\HttpAdapter\ConfigurationInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\HttpAdapter\MultiHttpAdapterException;
use Ivory\HttpAdapter\PsrHttpAdapterDecorator;
use Ivory\HttpAdapter\PsrHttpAdapterInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PsrHttpAdapterDecoratorTest extends AbstractTestCase
{
    /**
     * @var PsrHttpAdapterDecorator
     */
    private $decorator;

    /**
     * @var PsrHttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpAdapter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->decorator = new PsrHttpAdapterDecorator(
            $this->httpAdapter = $this->createMock('Ivory\HttpAdapter\PsrHttpAdapterInterface')
        );
    }

    public function testGetConfiguration()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration = $this->createConfigurationMock()));

        $this->assertSame($configuration, $this->decorator->getConfiguration());
    }

    public function testSetConfiguration()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('setConfiguration')
            ->with($this->identicalTo($configuration = $this->createConfigurationMock()));

        $this->decorator->setConfiguration($configuration);
    }

    public function testGetName()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'foo'));

        $this->assertSame($name, $this->decorator->getName());
    }

    public function testSendRequest()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($request = $this->createInternalRequestMock()))
            ->will($this->returnValue($response = $this->createResponseMock()));

        $this->assertSame($response, $this->decorator->sendRequest($request));
    }

    public function testSendRequestThrowException()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($request = $this->createInternalRequestMock()))
            ->will($this->throwException($exception = $this->createExceptionMock()));

        try {
            $this->decorator->sendRequest($request);
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertSame($e, $exception);
        }
    }

    public function testSendRequests()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('sendRequests')
            ->with($this->identicalTo($requests = [$this->createInternalRequestMock()]))
            ->will($this->returnValue($responses = [$this->createResponseMock()]));

        $this->assertSame($responses, $this->decorator->sendRequests($requests));
    }

    public function testSendRequestsThrowException()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('sendRequests')
            ->with($this->identicalTo($requests = [$this->createInternalRequestMock()]))
            ->will($this->throwException($exception = $this->createMultiExceptionMock(
                $responses = [$this->createResponseMock()],
                $exceptions = [$this->createExceptionMock()]
            )));

        try {
            $this->decorator->sendRequests($requests);
            $this->fail();
        } catch (MultiHttpAdapterException $e) {
            $this->assertSame($responses, $e->getResponses());
            $this->assertSame($exceptions, $e->getExceptions());
        }
    }

    /**
     * @return ConfigurationInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createConfigurationMock()
    {
        return $this->createMock('Ivory\HttpAdapter\ConfigurationInterface');
    }

    /**
     * @return InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createInternalRequestMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * @return ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createResponseMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * @return HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createExceptionMock()
    {
        return $this->createMock('Ivory\HttpAdapter\HttpAdapterException');
    }

    /**
     * @param array $responses
     * @param array $exceptions
     *
     * @return MultiHttpAdapterException|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createMultiExceptionMock(array $responses, array $exceptions)
    {
        $multiException = $this->createMock('Ivory\HttpAdapter\MultiHttpAdapterException');

        $multiException
            ->expects($this->any())
            ->method('getResponses')
            ->will($this->returnValue($responses));

        $multiException
            ->expects($this->any())
            ->method('getExceptions')
            ->will($this->returnValue($exceptions));

        return $multiException;
    }
}
