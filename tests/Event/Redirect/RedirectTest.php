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

    /**
     * @dataProvider validStatusCodeProvider
     */
    public function testCreateRedirectRequestWithoutRedirectResponse($statusCode)
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

        $this->assertFalse($this->redirect->createRedirectRequest(
            $response,
            $this->createRequestMock(),
            $this->createHttpAdapterMock()
        ));
    }

    /**
     * @dataProvider maxRedirectReachedProvider
     */
    public function testCreateRedirectRequestWithMaxRedirectReachedReturnFalse($redirectCount, $max)
    {
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
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo(Redirect::REDIRECT_COUNT))
            ->will($this->returnValue($redirectCount));

        $this->redirect->setThrowException(false);
        $this->redirect->setMax($max);

        $this->assertFalse($this->redirect->createRedirectRequest(
            $response,
            $request,
            $this->createHttpAdapterMock()
        ));
    }

    /**
     * @dataProvider maxRedirectReachedProvider
     */
    public function testCreateRedirectRequestWithMaxRedirectReachedThrowException($redirectCount, $max)
    {
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
            ->expects($this->exactly(2))
            ->method('getParameter')
            ->will($this->returnValueMap(array(
                array(Redirect::REDIRECT_COUNT, $redirectCount),
                array(Redirect::PARENT_REQUEST, $rootRequest = $this->createRequestMock()),
            )));

        $this->redirect->setThrowException(true);
        $this->redirect->setMax($max);

        try {
            $this->redirect->createRedirectRequest($response, $request, $this->createHttpAdapterMock());
            $this->fail();
        } catch (HttpAdapterException $e) {
            $this->assertSame(
                'An error occurred when fetching the URI "http://egeloen.fr" with the adapter "http_adapter" ("Max redirects exceeded (5)").',
                $e->getMessage()
            );

            $this->assertSame($rootRequest, $e->getRequest());
        }
    }

    /**
     * @dataProvider redirectStatusCodeProvider
     */
    public function testCreateRedirectRequestWithRedirectResponse($statusCode, $strict = false, $clear = true)
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

        $response
            ->expects($this->any())
            ->method('getHeaderLine')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue($location = 'http://egeloen.fr/'));

        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue($method = InternalRequestInterface::METHOD_POST));

        $request
            ->expects($this->any())
            ->method('getProtocolVersion')
            ->will($this->returnValue($protocolVersion = InternalRequestInterface::PROTOCOL_VERSION_1_0));

        $request
            ->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue($headers = array('header' => array('foo'))));

        $request
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($body = $this->getMock('Psr\Http\Message\StreamInterface')));

        $request
            ->expects($this->any())
            ->method('getDatas')
            ->will($this->returnValue($datas = array('data' => 'foo')));

        $request
            ->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue($files = array('file' => 'foo')));

        $request
            ->expects($this->any())
            ->method('getParameters')
            ->will($this->returnValue($parameters = array('parameter' => 'foo')));

        $messageFactory = $this->createMessageFactoryMock();
        $messageFactory
            ->expects($this->once())
            ->method('createInternalRequest')
            ->with(
                $this->identicalTo($location),
                $this->identicalTo($clear ? InternalRequestInterface::METHOD_GET : $method),
                $this->identicalTo($protocolVersion),
                $this->identicalTo($headers),
                $this->identicalTo($clear ? array() : $datas),
                $this->identicalTo($clear ? array() : $files),
                $this->identicalTo($parameters)
            )
            ->will($this->returnValue($redirectRequest = $this->createRequestMock()));

        if ($clear) {
            $redirectRequest
                ->expects($this->exactly(2))
                ->method('withoutHeader')
                ->will($this->returnValueMap(array(
                    array('Content-Type', $redirectRequest),
                    array('Content-Length', $redirectRequest),
                )));
        } else {
            $redirectRequest
                ->expects($this->once())
                ->method('withBody')
                ->with($this->identicalTo($body))
                ->will($this->returnValue($redirectRequest));
        }

        $redirectRequest
            ->expects($this->exactly(2))
            ->method('withParameter')
            ->will($this->returnValueMap(array(
                array(Redirect::PARENT_REQUEST, $request, $redirectRequest),
                array(Redirect::REDIRECT_COUNT, 1, $redirectRequest),
            )));

        $this->redirect->setStrict($strict);

        $this->assertSame($redirectRequest, $this->redirect->createRedirectRequest(
            $response,
            $request,
            $this->createHttpAdapterMock($messageFactory)
        ));
    }

    public function testPrepareResponse()
    {
        $request = $this->createRequestMock();
        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(Redirect::REDIRECT_COUNT))
            ->will($this->returnValue($redirectCount = 1));

        $request
            ->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue($uri = 'http://egeloen.fr/'));

        $response = $this->createResponseMock();
        $response
            ->expects($this->exactly(2))
            ->method('withParameter')
            ->will($this->returnValueMap(array(
                array(Redirect::REDIRECT_COUNT, $redirectCount, $response),
                array(Redirect::EFFECTIVE_URI, $uri, $response),
            )));

        $this->redirect->prepareResponse($response, $request);
    }

    public function testCreateRedirectRequestWillDiscardHostHeaderFromParentRequest()
    {
        $request = $this->createRequestMock();

        $headers = array('X-Foo' => 'Bar');

        $request
            ->expects($this->any())
            ->method('getHeaders')
            ->will($this->returnValue(array_merge($headers, array('Host' => 'egeloen.fr'))));

        $request
            ->expects($this->any())
            ->method('getParameter')
            ->with($this->identicalTo(Redirect::REDIRECT_COUNT))
            ->will($this->returnValue(0));

        $request
            ->expects($this->any())
            ->method('getProtocolVersion')
            ->will($this->returnValue($protocolVersion = InternalRequestInterface::PROTOCOL_VERSION_1_0));

        $request
            ->expects($this->any())
            ->method('getParameters')
            ->will($this->returnValue($parameters = array()));

        $request
            ->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($body = $this->getMock('Psr\Http\Message\StreamInterface')));

        $response = $this->createResponseMock();

        $response
            ->method('getStatusCode')
            ->will($this->returnValue(301));

        $response
            ->method('getHeaderLine')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue($location = 'http://google.com'));

        $response
            ->method('hasHeader')
            ->will($this->returnValue(true));

        $messageFactory = $this->createMessageFactoryMock();

        $messageFactory
            ->expects($this->once())
            ->method('createInternalRequest')
            ->with(
                $this->identicalTo($location),
                $this->identicalTo(InternalRequestInterface::METHOD_GET),
                $this->identicalTo($protocolVersion),
                $this->identicalTo($headers),
                $this->identicalTo(array()),
                $this->identicalTo(array()),
                $this->identicalTo($parameters)
            )
            ->will($this->returnValue($redirectRequest = $this->createRequestMock()));

        $redirectRequest
            ->expects($this->exactly(2))
            ->method('withoutHeader')
            ->will($this->returnValueMap(array(
                array('Content-Type', $redirectRequest),
                array('Content-Length', $redirectRequest),
            )));

        $redirectRequest
            ->expects($this->exactly(2))
            ->method('withParameter')
            ->will($this->returnValueMap(array(
                array(Redirect::PARENT_REQUEST, $request, $redirectRequest),
                array(Redirect::REDIRECT_COUNT, 1, $redirectRequest),
            )));

        $this->redirect->setStrict(false);

        $httpAdapter = $this->createHttpAdapterMock($messageFactory);

        $this->assertSame($redirectRequest, $this->redirect->createRedirectRequest($response, $request, $httpAdapter));
    }

    /**
     * Gets the valid status code provider.
     *
     * @return array The valid status code provider.
     */
    public function validStatusCodeProvider()
    {
        return array(
            array(100),
            array(200),
            array(400),
            array(500),
        );
    }

    /**
     * Gets the redirect status code provider.
     *
     * @return array The redirect status code provider.
     */
    public function redirectStatusCOdeProvider()
    {
        return array(
            array(300),
            array(301),
            array(302),
            array(303),
            array(304, false, false),
            array(305, false, false),
            array(300, true, false),
            array(301, true, false),
            array(302, true, false),
            array(303, true),
            array(304, false, false),
            array(305, false, false),
        );
    }

    /**
     * Gets the max redirect reached  provider.
     *
     * @return array The max redirect reached provider.
     */
    public function maxRedirectReachedProvider()
    {
        return array(
            array(5, 5),
            array(6, 5),
            array(10, 5),
        );
    }

    /**
     * Creates a request mock.
     *
     * @param \Ivory\HttpAdapter\Message\InternalRequestInterface|null $parent The parent request.
     *
     * @return \Ivory\HttpAdapter\Message\InternalRequestInterface|\PHPUnit_Framework_MockObject_MockObject The request mock.
     */
    private function createRequestMock($parent = null)
    {
        $request = $this->getMock('Ivory\HttpAdapter\Message\InternalRequestInterface');
        $request
            ->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue('http://egeloen.fr'));

        if ($parent !== null) {
            $request
                ->expects($this->any())
                ->method('hasParameter')
                ->with($this->identicalTo(Redirect::PARENT_REQUEST))
                ->will($this->returnValue(true));

            $request
                ->expects($this->any())
                ->method('getParameter')
                ->with($this->identicalTo(Redirect::PARENT_REQUEST))
                ->will($this->returnValue($parent));
        }

        return $request;
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
            ->method('getName')
            ->will($this->returnValue('http_adapter'));

        $httpAdapter
            ->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration = $this->getMock('Ivory\HttpAdapter\ConfigurationInterface')));

        $configuration
            ->expects($this->any())
            ->method('getMessageFactory')
            ->will($this->returnValue($messageFactory ?: $this->createMessageFactoryMock()));

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
}
