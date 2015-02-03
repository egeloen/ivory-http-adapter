<?php

/**
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Redirect;

use Ivory\HttpAdapter\Event\Redirect\Redirect;
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
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     * @expectedExceptionMessage An error occurred when fetching the URL "http://egeloen.fr" with the adapter "http_adapter" ("Max redirects exceeded (5)")
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

        $this->redirect->createRedirectRequest($response, $request, $this->createHttpAdapterMock());
    }

    /**
     * @dataProvider redirectStatusCodeProvider
     */
    public function testCreateRedirectRequestWithRedirectResponse($statusCode, $strict = false, $clear = true)
    {
        $messageFactory = $this->createMessageFactoryMock();
        $messageFactory
            ->expects($this->once())
            ->method('cloneInternalRequest')
            ->with($this->identicalTo($request = $this->createRequestMock()))
            ->will($this->returnValue($redirectRequest = $this->createRequestMock()));

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
            ->method('getHeader')
            ->with($this->identicalTo('Location'))
            ->will($this->returnValue($location = 'http://egeloen.fr'));

        $redirectRequest
            ->expects($clear ? $this->once() : $this->never())
            ->method('setMethod')
            ->with($this->identicalTo(InternalRequestInterface::METHOD_GET));

        $redirectRequest
            ->expects($clear ? $this->once() : $this->never())
            ->method('removeHeaders')
            ->with($this->identicalTo(array('Content-Type', 'Content-Length')));

        $redirectRequest
            ->expects($clear ? $this->once() : $this->never())
            ->method('clearRawDatas');

        $redirectRequest
            ->expects($clear ? $this->once() : $this->never())
            ->method('clearDatas');

        $redirectRequest
            ->expects($clear ? $this->once() : $this->never())
            ->method('clearFiles');

        $redirectRequest
            ->expects($this->once())
            ->method('setUrl')
            ->with($this->identicalTo($location));

        $redirectRequest
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                array($this->identicalTo(Redirect::PARENT_REQUEST), $this->identicalTo($request)),
                array($this->identicalTo(Redirect::REDIRECT_COUNT), $this->identicalTo(1))
            );

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
            ->method('getUrl')
            ->will($this->returnValue($url = 'http://egeloen.fr'));

        $response = $this->createResponseMock();
        $response
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                array($this->identicalTo(Redirect::REDIRECT_COUNT), $this->identicalTo($redirectCount)),
                array($this->identicalTo(Redirect::EFFECTIVE_URL), $this->identicalTo($url))
            );

        $this->redirect->prepareResponse($response, $request);
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
            ->method('getUrl')
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
