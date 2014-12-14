<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Redirect;

use Ivory\HttpAdapter\Event\Redirect\Redirect;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\MessageFactoryInterface;

/**
 * Redirect test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class RedirectTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\Event\Redirect\Redirect */
    private $redirect;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->redirect = new Redirect();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->redirect);
    }

    public function testDefaultState()
    {
        $this->assertSame(5, $this->redirect->getMax());
        $this->assertFalse($this->redirect->isStrict());
        $this->assertTrue($this->redirect->getThrowException());
    }

    public function testInitialState()
    {
        $this->redirect = new Redirect($max = 10, true, false);

        $this->assertSame($max, $this->redirect->getMax());
        $this->assertTrue($this->redirect->isStrict());
        $this->assertFalse($this->redirect->getThrowException());
    }

    public function testSetMax()
    {
        $this->redirect->setMax($max = 10);

        $this->assertSame($max, $this->redirect->getMax());
    }

    public function testStrict()
    {
        $this->redirect->setStrict(true);

        $this->assertTrue($this->redirect->isStrict());
    }

    public function testSetThrowException()
    {
        $this->redirect->setThrowException(false);

        $this->assertFalse($this->redirect->getThrowException());
    }

    public function testRedirectWithoutRedirect()
    {
        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue($url = 'http://egeloen.fr'));

        $response = $this->createResponseMock();
        $response
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                array($this->identicalTo(Redirect::REDIRECT_COUNT), $this->identicalTo(0)),
                array($this->identicalTo(Redirect::EFFECTIVE_URL), $this->identicalTo($url))
            );

        $this->assertSame($response, $this->redirect->redirect($response, $request, $this->createHttpAdapterMock()));
    }

    /**
     * @dataProvider statusCodeProvider
     */
    public function testRedirectNotStictly($statusCode)
    {
        $response = $this->createResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $response
            ->expects($this->any())
            ->method('hasHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue(true));

        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(Redirect::REDIRECT_COUNT))
            ->will($this->returnValue(null));

        $response
            ->expects($this->any())
            ->method('getHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue($location = 'http://egeloen.fr'));

        $messageFactory = $this->createMessageFactoryMock();
        $messageFactory
            ->expects($this->once())
            ->method('cloneInternalRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($requestClone = $this->createRequestMock()));

        $requestClone
            ->expects($this->once())
            ->method('setMethod')
            ->with($this->identicalTo(InternalRequestInterface::METHOD_GET));

        $requestClone
            ->expects($this->once())
            ->method('removeHeaders')
            ->with($this->identicalTo(array('Content-Type', 'Content-Length')));

        $requestClone
            ->expects($this->once())
            ->method('clearRawDatas');

        $requestClone
            ->expects($this->once())
            ->method('clearDatas');

        $requestClone
            ->expects($this->once())
            ->method('clearFiles');

        $requestClone
            ->expects($this->once())
            ->method('setUrl')
            ->with($this->identicalTo($location));

        $requestClone
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                array($this->identicalTo(Redirect::PARENT_REQUEST), $this->identicalTo($request)),
                array($this->identicalTo(Redirect::REDIRECT_COUNT), $this->identicalTo(1))
            );

        $httpAdapter = $this->createHttpAdapterMock($messageFactory);
        $httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($requestClone))
            ->will($this->returnValue($redirectResponse = $this->createResponseMock()));

        $this->assertSame($redirectResponse, $this->redirect->redirect($response, $request, $httpAdapter));
    }

    /**
     * @dataProvider strictStatusCodeProvider
     */
    public function testRedirectStrictly($statusCode)
    {
        $this->redirect->setStrict(true);

        $response = $this->createResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue($statusCode));

        $response
            ->expects($this->any())
            ->method('hasHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue(true));

        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(Redirect::REDIRECT_COUNT))
            ->will($this->returnValue(null));

        $response
            ->expects($this->any())
            ->method('getHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue($location = 'http://egeloen.fr'));

        $messageFactory = $this->createMessageFactoryMock();
        $messageFactory
            ->expects($this->once())
            ->method('cloneInternalRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($requestClone = $this->createRequestMock()));

        $requestClone
            ->expects($this->never())
            ->method('setMethod');

        $requestClone
            ->expects($this->never())
            ->method('removeHeaders');

        $requestClone
            ->expects($this->never())
            ->method('clearRawDatas');

        $requestClone
            ->expects($this->never())
            ->method('clearDatas');

        $requestClone
            ->expects($this->never())
            ->method('clearFiles');

        $requestClone
            ->expects($this->once())
            ->method('setUrl')
            ->with($this->identicalTo($location));

        $requestClone
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                array($this->identicalTo(Redirect::PARENT_REQUEST), $this->identicalTo($request)),
                array($this->identicalTo(Redirect::REDIRECT_COUNT), $this->identicalTo(1))
            );

        $httpAdapter = $this->createHttpAdapterMock($messageFactory);
        $httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($requestClone))
            ->will($this->returnValue($redirectResponse = $this->createResponseMock()));

        $this->assertSame($redirectResponse, $this->redirect->redirect($response, $request, $httpAdapter));
    }

    public function testRedirect303NotStrictly()
    {
        $response = $this->createResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(303));

        $response
            ->expects($this->any())
            ->method('hasHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue(true));

        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(Redirect::REDIRECT_COUNT))
            ->will($this->returnValue(null));

        $response
            ->expects($this->any())
            ->method('getHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue($location = 'http://egeloen.fr'));

        $messageFactory = $this->createMessageFactoryMock();
        $messageFactory
            ->expects($this->once())
            ->method('cloneInternalRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($requestClone = $this->createRequestMock()));

        $requestClone
            ->expects($this->once())
            ->method('setMethod')
            ->with($this->identicalTo(InternalRequestInterface::METHOD_GET));

        $requestClone
            ->expects($this->once())
            ->method('removeHeaders')
            ->with($this->identicalTo(array('Content-Type', 'Content-Length')));

        $requestClone
            ->expects($this->once())
            ->method('clearRawDatas');

        $requestClone
            ->expects($this->once())
            ->method('clearDatas');

        $requestClone
            ->expects($this->once())
            ->method('clearFiles');

        $requestClone
            ->expects($this->once())
            ->method('setUrl')
            ->with($this->identicalTo($location));

        $requestClone
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                array($this->identicalTo(Redirect::PARENT_REQUEST), $this->identicalTo($request)),
                array($this->identicalTo(Redirect::REDIRECT_COUNT), $this->identicalTo(1))
            );

        $httpAdapter = $this->createHttpAdapterMock($messageFactory);
        $httpAdapter
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->identicalTo($requestClone))
            ->will($this->returnValue($redirectResponse = $this->createResponseMock()));

        $this->assertSame($redirectResponse, $this->redirect->redirect($response, $request, $httpAdapter));
    }

    public function testRedirectExceededThrowException()
    {
        $this->redirect->setMax($max = 1);

        $response = $this->createResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(300));

        $response
            ->expects($this->any())
            ->method('hasHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue(true));

        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('hasParameter')
            ->with($this->identicalTo(Redirect::PARENT_REQUEST))
            ->will($this->returnValue(true));

        $request
            ->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValueMap(array(
                array(Redirect::REDIRECT_COUNT, 1),
                array(Redirect::PARENT_REQUEST, $parentRequest = $this->createRequestMock()),
            )));

        $parentRequest
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue($url = 'http://egeloen.fr'));

        $httpAdapter = $this->createHttpAdapterMock();
        $httpAdapter
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($httpAdapterName = 'name'));

        try {
            $this->redirect->redirect($response, $request, $httpAdapter);
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertContains($url, $e->getMessage());
            $this->assertContains((string) $max, $e->getMessage());
            $this->assertContains($httpAdapterName, $e->getMessage());
        }
    }

    public function testRedirectExceededDontThrowException()
    {
        $this->redirect->setMax(1);
        $this->redirect->setThrowException(false);

        $response = $this->createResponseMock();
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(300));

        $response
            ->expects($this->any())
            ->method('hasHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue(true));

        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(Redirect::REDIRECT_COUNT))
            ->will($this->returnValue($redirectCount = 1));

        $request
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue($url = 'http://egeloen.fr'));

        $response
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                array($this->identicalTo(Redirect::REDIRECT_COUNT), $this->identicalTo($redirectCount)),
                array($this->identicalTo(Redirect::EFFECTIVE_URL), $this->identicalTo($url))
            );

        $this->assertSame($response, $this->redirect->redirect($response, $request, $this->createHttpAdapterMock()));
    }

    /**
     * Gets the status code provider.
     *
     * @return array The status code provider.
     */
    public function statusCodeProvider()
    {
        return array_merge(
            $this->strictStatusCodeProvider(),
            array(array(303))
        );
    }

    /**
     * Gets the strict status code provider.
     *
     * @return array The strict status code provider.
     */
    public function strictStatusCodeProvider()
    {
        return array(
            array(300),
            array(301),
            array(302),
        );
    }

    /**
     * Creates a request mock.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The request mock.
     */
    private function createRequestMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
    }

    /**
     * Creates a response mock.
     *
     * @return \Ivory\HttpAdapter\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject The response mock.
     */
    private function createResponseMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\ResponseInterface');
    }

    /**
     * Creates an http adapter mock.
     *
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface|null $messageFactory The message factory.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject The http adapter mock.
     */
    private function createHttpAdapterMock(MessageFactoryInterface $messageFactory = null)
    {
        $httpAdapter = $this->getMock('Ivory\HttpAdapter\HttpAdapterInterface');
        $httpAdapter
            ->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($this->createConfigurationMock($messageFactory)));

        return $httpAdapter;
    }

    /**
     * Creates a message factory mock.
     *
     * @return \Ivory\HttpAdapter\Message\MessageFactoryInterface|\PHPUnit_Framework_MockObject_MockObject The message factory mock.
     */
    private function createMessageFactoryMock()
    {
        return $this->getMock('Ivory\HttpAdapter\Message\MessageFactoryInterface');
    }

    /**
     * Creates a configuration mock.
     *
     * @param \Ivory\HttpAdapter\Message\MessageFactoryInterface|null $messageFactory The message factory.
     *
     * @return \Ivory\HttpAdapter\ConfigurationInterface|\PHPUnit_Framework_MockObject_MockObject The configuration mock.
     */
    private function createConfigurationMock(MessageFactoryInterface $messageFactory = null)
    {
        $configuration = $this->getMock('Ivory\HttpAdapter\ConfigurationInterface');
        $configuration
            ->expects($this->any())
            ->method('getMessageFactory')
            ->will($this->returnValue($messageFactory));

        return $configuration;
    }
}
