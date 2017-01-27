<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Event\Cache\Adapter;

use Ivory\HttpAdapter\Event\Cache\Adapter\CacheAdapterInterface;
use Ivory\HttpAdapter\Event\Cache\Cache;
use Ivory\HttpAdapter\Event\Formatter\FormatterInterface;
use Ivory\HttpAdapter\HttpAdapterException;
use Ivory\HttpAdapter\Message\InternalRequestInterface;
use Ivory\HttpAdapter\Message\MessageFactoryInterface;
use Ivory\HttpAdapter\Message\RequestInterface;
use Ivory\HttpAdapter\Message\ResponseInterface;
use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CacheTest extends AbstractTestCase
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var CacheAdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $adapter;

    /**
     * @var FormatterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $formatter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->adapter = $this->createCacheAdapterMock();
        $this->formatter = $this->createFormatterMock();
        $this->cache = new Cache($this->adapter, $this->formatter);
    }

    public function testDefaultState()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Event\Cache\CacheInterface', $this->cache);
        $this->assertSame($this->adapter, $this->cache->getAdapter());
        $this->assertSame($this->formatter, $this->cache->getFormatter());
        $this->assertNull($this->cache->getLifeTime());
        $this->assertTrue($this->cache->cacheException());
    }

    public function testInitialState()
    {
        $this->cache = new Cache($this->adapter, $this->formatter, $lifeTime = 100, false);

        $this->assertSame($lifeTime, $this->cache->getLifeTime());
        $this->assertFalse($this->cache->cacheException());
    }

    public function testSetAdapter()
    {
        $this->cache->setAdapter($adapter = $this->createCacheAdapterMock());

        $this->assertSame($adapter, $this->cache->getAdapter());
    }

    public function testSetFormatter()
    {
        $this->cache->setFormatter($formatter = $this->createFormatterMock());

        $this->assertSame($formatter, $this->cache->getFormatter());
    }

    public function testSetCacheException()
    {
        $this->cache->cacheException(false);

        $this->assertFalse($this->cache->cacheException());
    }

    public function testGetResponseWithCachedResponse()
    {
        $request = $this->createInternalRequestMock();

        $this->formatter
            ->expects($this->once())
            ->method('formatRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue(['request']));

        $this->adapter
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($id = 'cd026c3d697e7e0ba79cdc3d0b054a4c65b84f2f'))
            ->will($this->returnValue(true));

        $this->adapter
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($id))
            ->will($this->returnValue(json_encode([
                'protocol_version' => $protocolVersion = RequestInterface::PROTOCOL_VERSION_1_1,
                'status_code'      => $statusCode = 200,
                'headers'          => $headers = ['foo' => 'bar'],
                'body'             => $body = 'body',
                'parameters'       => $parameters = ['baz' => 'bat'],
            ])));

        $messageFactory = $this->createMessageFactoryMock();

        $messageFactory
            ->expects($this->once())
            ->method('createResponse')
            ->with(
                $this->identicalTo($statusCode),
                $this->identicalTo($protocolVersion),
                $this->identicalTo($headers),
                $this->identicalTo($body),
                $this->identicalTo($parameters)
            )
            ->will($this->returnValue($response = $this->createResponseMock()));

        $response
            ->expects($this->once())
            ->method('withParameter')
            ->with($this->identicalTo('request'), $this->identicalTo($request))
            ->will($this->returnValue($finalResponse = $this->createResponseMock()));

        $this->assertSame($finalResponse, $this->cache->getResponse($request, $messageFactory));
    }

    public function testGetResponseWithoutCachedResponse()
    {
        $request = $this->createInternalRequestMock();

        $this->formatter
            ->expects($this->once())
            ->method('formatRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue(['request']));

        $this->adapter
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($id = 'cd026c3d697e7e0ba79cdc3d0b054a4c65b84f2f'))
            ->will($this->returnValue(false));

        $this->adapter
            ->expects($this->never())
            ->method('get');

        $this->assertNull($this->cache->getResponse($request, $this->createMessageFactoryMock()));
    }

    public function testGetExceptionWithCachedException()
    {
        $request = $this->createInternalRequestMock();

        $this->formatter
            ->expects($this->once())
            ->method('formatRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue(['request']));

        $this->adapter
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($id = '92bddc469be2d3193975f36b54c1c3ae470b11fd'))
            ->will($this->returnValue(true));

        $this->adapter
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($id))
            ->will($this->returnValue(json_encode(['message' => $message = 'message'])));

        $exception = $this->cache->getException($request, $this->createMessageFactoryMock());

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($request, $exception->getRequest());
        $this->assertNull($exception->getResponse());
    }

    public function testGetExceptionWithoutCachedException()
    {
        $request = $this->createInternalRequestMock();

        $this->formatter
            ->expects($this->once())
            ->method('formatRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue(['request']));

        $this->adapter
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($id = '92bddc469be2d3193975f36b54c1c3ae470b11fd'))
            ->will($this->returnValue(false));

        $this->adapter
            ->expects($this->never())
            ->method('get');

        $this->assertNull($this->cache->getException($request, $this->createMessageFactoryMock()));
    }

    public function testGetExceptionWithCachedExceptionButDisabled()
    {
        $this->cache->cacheException(false);

        $this->assertNull($this->cache->getException(
            $this->createInternalRequestMock(),
            $this->createMessageFactoryMock()
        ));
    }

    public function testSaveResponse()
    {
        $request = $this->createInternalRequestMock();

        $this->formatter
            ->expects($this->once())
            ->method('formatRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue(['request']));

        $this->formatter
            ->expects($this->once())
            ->method('formatResponse')
            ->with($this->identicalTo($response = $this->createResponseMock()))
            ->will($this->returnValue($formattedResponse = ['response']));

        $this->adapter
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($id = 'cd026c3d697e7e0ba79cdc3d0b054a4c65b84f2f'))
            ->will($this->returnValue(false));

        $this->adapter
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->identicalTo($id),
                $this->identicalTo(json_encode($formattedResponse, true)),
                $this->identicalTo($lifeTime = 100)
            );

        $this->cache->setLifeTime($lifeTime);
        $this->cache->saveResponse($response, $request);
    }

    public function testSaveResponseWithAlreadySaved()
    {
        $request = $this->createInternalRequestMock();

        $this->formatter
            ->expects($this->once())
            ->method('formatRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue(['request']));

        $this->adapter
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($id = 'cd026c3d697e7e0ba79cdc3d0b054a4c65b84f2f'))
            ->will($this->returnValue(true));

        $this->adapter
            ->expects($this->never())
            ->method('set');

        $this->cache->saveResponse($this->createResponseMock(), $request);
    }

    public function testSaveException()
    {
        $request = $this->createInternalRequestMock();

        $this->formatter
            ->expects($this->once())
            ->method('formatRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue(['request']));

        $this->formatter
            ->expects($this->once())
            ->method('formatException')
            ->with($this->identicalTo($exception = $this->createExceptionMock()))
            ->will($this->returnValue($formattedException = ['exception']));

        $this->adapter
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($id = '92bddc469be2d3193975f36b54c1c3ae470b11fd'))
            ->will($this->returnValue(false));

        $this->adapter
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->identicalTo($id),
                $this->identicalTo(json_encode($formattedException, true)),
                $this->identicalTo($lifeTime = 100)
            );

        $this->cache->setLifeTime($lifeTime);
        $this->cache->saveException($exception, $request);
    }

    public function testSaveExceptionDisabled()
    {
        $this->adapter
            ->expects($this->never())
            ->method('has');

        $this->adapter
            ->expects($this->never())
            ->method('set');

        $this->cache->cacheException(false);
        $this->cache->saveException($this->createExceptionMock(), $this->createInternalRequestMock());
    }

    public function testSaveExceptionAlreadySaved()
    {
        $request = $this->createInternalRequestMock();

        $this->formatter
            ->expects($this->once())
            ->method('formatRequest')
            ->with($this->identicalTo($request))
            ->will($this->returnValue(['request']));

        $this->adapter
            ->expects($this->once())
            ->method('has')
            ->with($this->identicalTo($id = '92bddc469be2d3193975f36b54c1c3ae470b11fd'))
            ->will($this->returnValue(true));

        $this->adapter
            ->expects($this->never())
            ->method('set');

        $this->cache->saveException($this->createExceptionMock(), $request);
    }

    /**
     * @return CacheAdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createCacheAdapterMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\Cache\Adapter\CacheAdapterInterface');
    }

    /**
     * @return FormatterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createFormatterMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Event\Formatter\FormatterInterface');
    }

    /**
     * @return MessageFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createMessageFactoryMock()
    {
        return $this->createMock('Ivory\HttpAdapter\Message\MessageFactoryInterface');
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
}
