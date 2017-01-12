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

use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\MultiHttpAdapterException;
use Ivory\HttpAdapter\PsrHttpAdapterDecorator;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PsrHttpAdapterDecoratorTest extends AbstractTestCase
{
    /** @var  \Ivory\HttpAdapter\PsrHttpAdapterDecorator */
    private $decorator;

    /** @var \Ivory\HttpAdapter\PsrHttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
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

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->decorator);
        unset($this->httpAdapter);
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
            ->with($this->identicalTo($requests = array($this->createInternalRequestMock())))
            ->will($this->returnValue($responses = array($this->createResponseMock())));

        $this->assertSame($responses, $this->decorator->sendRequests($requests));
    }

    public function testSendRequestsThrowException()
    {
        $this->httpAdapter
            ->expects($this->once())
            ->method('sendRequests')
            ->with($this->identicalTo($requests = array($this->createInternalRequestMock())))
            ->will($this->throwException($exception = $this->createMultiExceptionMock(
                $responses = array($this->createResponseMock()),
                $exceptions = array($this->createExceptionMock())
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
     * Creates a configuration mock.
     *
     * @return \Ivory\HttpAdapter\ConfigurationInterface|\PHPUnit_Framework_MockObject_MockObject The configuration mock.
     */
    private function createConfigurationMock()
    {
        return $this->createMock('Ivory\HttpAdapter\ConfigurationInterface');
    }

    /**
     * Creates an internal request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The internal request mock.
     */
    private function createInternalRequestMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    private function createResponseMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * Creates an exception mock.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterException|\PHPUnit_Framework_MockObject_MockObject The exception mock.
     */
    private function createExceptionMock()
    {
        return $this->createMock('Ivory\HttpAdapter\HttpAdapterException');
    }

    /**
     * Creates a multi exception mock.
     *
     * @param array $responses  The responses.
     * @param array $exceptions The exceptions.
     *
     * @return \Ivory\HttpAdapter\MultiHttpAdapterException|\PHPUnit_Framework_MockObject_MockObject The mutli exception mock.
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
