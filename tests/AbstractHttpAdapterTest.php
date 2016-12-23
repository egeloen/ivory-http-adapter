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

use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\InternalRequest;
use Ivory\HttpAdapter\Message\Request;
use Ivory\HttpAdapter\MultiHttpAdapterException;
use Ivory\Tests\HttpAdapter\Utility\PHPUnitUtility;

/**
 * Abstract http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractHttpAdapterTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    private static $file;

    /** @var \Ivory\HttpAdapter\HttpAdapterInterface */
    protected $httpAdapter;

    /** @var array */
    protected $defaultOptions;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        self::$file = PHPUnitUtility::getFile(true, 'ivory-http-adapter.log');
    }

    /**
     * {@inheritdoc}
     */
    public static function tearDownAfterClass()
    {
        if (file_exists(self::$file)) {
            unlink(self::$file);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->defaultOptions = array(
            'protocol_version' => Request::PROTOCOL_VERSION_1_1,
            'status_code'      => 200,
            'reason_phrase'    => 'OK',
            'headers'          => array('Content-Type' => 'text/html'),
            'body'             => 'Ok',
        );

        $this->httpAdapter = $this->createHttpAdapter();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->httpAdapter);
    }

    abstract public function testGetName();

    public function testInheritance()
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\AbstractHttpAdapter', $this->httpAdapter);
    }

    /**
     * @dataProvider simpleProvider
     * @group integration
     */
    public function testGet($uri, array $headers = array())
    {
        $this->assertResponse($this->httpAdapter->get($uri, $headers));
        $this->assertRequest(Request::METHOD_GET, $headers);
    }

    /**
     * @dataProvider simpleProvider
     * @group integration
     */
    public function testHead($uri, array $headers = array())
    {
        $this->assertResponse($this->httpAdapter->head($uri, $headers), array('body' => null));
        $this->assertRequest(Request::METHOD_HEAD, $headers);
    }

    /**
     * @dataProvider simpleProvider
     * @group integration
     */
    public function testTrace($uri, array $headers = array())
    {
        $response = $this->httpAdapter->trace($uri, $headers);

        if ($response->getStatusCode() === 405) {
            $this->markTestIncomplete();
        }

        $this->assertResponse($response);
        $this->assertRequest(Request::METHOD_TRACE, $headers);
    }

    /**
     * @dataProvider fullProvider
     * @group integration
     */
    public function testPost($uri, array $headers = array(), array $data = array(), array $files = array())
    {
        $this->assertResponse($this->httpAdapter->post($uri, $headers, $data, $files));
        $this->assertRequest(Request::METHOD_POST, $headers, $data, $files);
    }

    /**
     * @dataProvider fullProvider
     * @group integration
     */
    public function testPut($uri, array $headers = array(), array $data = array(), array $files = array())
    {
        $this->assertResponse($this->httpAdapter->put($uri, $headers, $data, $files));
        $this->assertRequest(Request::METHOD_PUT, $headers, $data, $files);
    }

    /**
     * @dataProvider fullProvider
     * @group integration
     */
    public function testPatch($uri, array $headers = array(), array $data = array(), array $files = array())
    {
        $this->assertResponse($this->httpAdapter->patch($uri, $headers, $data, $files));
        $this->assertRequest(Request::METHOD_PATCH, $headers, $data, $files);
    }

    /**
     * @dataProvider fullProvider
     * @group integration
     */
    public function testDelete($uri, array $headers = array(), array $data = array(), array $files = array())
    {
        $this->assertResponse($this->httpAdapter->delete($uri, $headers, $data, $files));
        $this->assertRequest(Request::METHOD_DELETE, $headers, $data, $files);
    }

    /**
     * @dataProvider fullProvider
     * @group integration
     */
    public function testOptions($uri, array $headers = array(), array $data = array(), array $files = array())
    {
        $this->assertResponse($this->httpAdapter->options($uri, $headers, $data, $files));
        $this->assertRequest(Request::METHOD_OPTIONS, $headers, $data, $files);
    }

    /**
     * @dataProvider requestProvider
     * @group integration
     */
    public function testSendRequest($uri, $method, array $headers = array(), array $data = array())
    {
        $request = $this->httpAdapter->getConfiguration()->getMessageFactory()->createRequest(
            $uri,
            $method,
            Request::PROTOCOL_VERSION_1_1,
            $headers,
            http_build_query($data, null, '&')
        );

        $options = array();
        if ($method === Request::METHOD_HEAD) {
            $options['body'] = null;
        }

        $response = $this->httpAdapter->sendRequest($request);

        if ($method === Request::METHOD_TRACE && $response->getStatusCode() === 405) {
            $this->markTestIncomplete();
        }

        $this->assertResponse($response, $options);
        $this->assertRequest($method, $headers, $data);
    }

    /**
     * @dataProvider internalRequestProvider
     * @group integration
     */
    public function testSendInternalRequest(
        $uri,
        $method,
        array $headers = array(),
        array $datas = array(),
        array $files = array()
    ) {
        $request = $this->httpAdapter->getConfiguration()->getMessageFactory()->createInternalRequest(
            $uri,
            $method,
            Request::PROTOCOL_VERSION_1_1,
            $headers,
            $datas,
            $files
        );

        $options = array();
        if ($method === Request::METHOD_HEAD) {
            $options['body'] = null;
        }

        $response = $this->httpAdapter->sendRequest($request);

        if ($method === Request::METHOD_TRACE && $response->getStatusCode() === 405) {
            $this->markTestIncomplete();
        }

        $this->assertResponse($response, $options);
        $this->assertRequest($method, $headers, $datas, $files);
    }

    /**
     * @group integration
     */
    public function testSendRequests()
    {
        $this->assertMultiResponses($this->httpAdapter->sendRequests($requests = $this->requestsProvider()), $requests);
    }

    /**
     * @group integration
     */
    public function testSendErroredRequests()
    {
        list($requests, $erroredRequests) = $this->erroredRequestsProvider();

        try {
            $this->httpAdapter->sendRequests(array_merge($requests, $erroredRequests));
            $this->fail();
        } catch (MultiHttpAdapterException $e) {
            $this->assertMultiResponses($e->getResponses(), $requests);
            $this->assertMultiExceptions($e->getExceptions(), $erroredRequests);
        }
    }

    /**
     * @group integration
     */
    public function testSendWithCustomArgSeparatorOutput()
    {
        $argSeparatorOutput = ini_get('arg_separator.output');
        ini_set('arg_separator.output', '&amp;');

        $this->assertResponse(
            $this->httpAdapter->post($this->getUri(), $headers = $this->getHeaders(), $data = $this->getData())
        );

        $this->assertRequest(Request::METHOD_POST, $headers, $data);

        ini_set('arg_separator.output', $argSeparatorOutput);
    }

    /**
     * @group integration
     */
    public function testSendWithProtocolVersion10()
    {
        $this->httpAdapter->getConfiguration()->setProtocolVersion($protocolVersion = Request::PROTOCOL_VERSION_1_0);
        $response = $this->httpAdapter->send($this->getUri(), $method = Request::METHOD_GET);

        $this->assertResponse($response, array('protocol_version' => $protocolVersion));
        $this->assertRequest($method, array(), array(), array(), $protocolVersion);
    }

    /**
     * @group integration
     */
    public function testSendWithUserAgent()
    {
        $this->httpAdapter->getConfiguration()->setUserAgent($userAgent = 'foo');

        $this->assertResponse($this->httpAdapter->send($this->getUri(), $method = Request::METHOD_GET));
        $this->assertRequest($method, array('User-Agent' => $userAgent));
    }

    /**
     * @group integration
     */
    public function testSendWithClientError()
    {
        $this->assertResponse(
            $this->httpAdapter->send($uri = $this->getClientErrorUri(), $method = Request::METHOD_GET),
            array(
                'status_code'   => 400,
                'reason_phrase' => 'Bad Request',
            )
        );

        $this->assertRequest($method);
    }

    /**
     * @group integration
     */
    public function testSendWithServerError()
    {
        $this->assertResponse(
            $this->httpAdapter->send($uri = $this->getServerErrorUri(), $method = Request::METHOD_GET),
            array(
                'status_code'   => 500,
                'reason_phrase' => 'Internal Server Error',
            )
        );

        $this->assertRequest($method);
    }

    /**
     * @group integration
     */
    public function testSendWithRedirect()
    {
        $this->assertResponse(
            $this->httpAdapter->send($uri = $this->getRedirectUri(), $method = Request::METHOD_GET),
            array(
                'status_code'   => 302,
                'reason_phrase' => 'Found',
                'body'          => 'Redirect',
            )
        );

        $this->assertRequest($method);
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     * @group integration
     */
    public function testSendWithInvalidUri()
    {
        $this->httpAdapter->send($this->getInvalidUri(), Request::METHOD_GET);
    }

    /**
     * @dataProvider timeoutProvider
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     * @group integration
     */
    public function testSendWithTimeoutExceeded($timeout)
    {
        $this->httpAdapter->getConfiguration()->setTimeout($timeout);
        $this->httpAdapter->send($this->getDelayUri($timeout), Request::METHOD_GET);
    }

    /**
     * Gets the simple provider.
     *
     * @return array The simple provider.
     */
    public function simpleProvider()
    {
        return array(
            array($this->getUri()),
            array($this->getUri(), $this->getHeaders()),
        );
    }

    /**
     * Gets the full provider.
     *
     * @return array The full provider.
     */
    public function fullProvider()
    {
        return array_merge(
            $this->simpleProvider(),
            array(
                array($this->getUri(), $this->getHeaders(), $this->getData()),
                array($this->getUri(), $this->getHeaders(), $this->getData(), $this->getFiles()),
            )
        );
    }

    /**
     * Gets the request provider.
     *
     * @return array The request provider.
     */
    public function requestProvider()
    {
        $requests = array();

        foreach ($this->internalRequestProvider() as $request) {
            if (!isset($request[4])) {
                $requests[] = $request;
            }
        }

        return $requests;
    }

    /**
     * Gets the internal request provider.
     *
     * @return array The internal request provider.
     */
    public function internalRequestProvider()
    {
        return array(
            array($this->getUri(), InternalRequest::METHOD_GET),
            array($this->getUri(), InternalRequest::METHOD_GET, $this->getHeaders()),
            array($this->getUri(), InternalRequest::METHOD_HEAD),
            array($this->getUri(), InternalRequest::METHOD_HEAD, $this->getHeaders()),
            array($this->getUri(), InternalRequest::METHOD_TRACE),
            array($this->getUri(), InternalRequest::METHOD_TRACE, $this->getHeaders()),
            array($this->getUri(), InternalRequest::METHOD_POST),
            array($this->getUri(), InternalRequest::METHOD_POST, $this->getHeaders()),
            array($this->getUri(), InternalRequest::METHOD_POST, $this->getHeaders(), $this->getData()),
            array(
                $this->getUri(),
                InternalRequest::METHOD_POST,
                $this->getHeaders(),
                $this->getData(),
                $this->getFiles(),
            ),
            array($this->getUri(), InternalRequest::METHOD_PUT),
            array($this->getUri(), InternalRequest::METHOD_PUT, $this->getHeaders()),
            array($this->getUri(), InternalRequest::METHOD_PUT, $this->getHeaders(), $this->getData()),
            array(
                $this->getUri(),
                InternalRequest::METHOD_PUT,
                $this->getHeaders(),
                $this->getData(),
                $this->getFiles(),
            ),
            array($this->getUri(), InternalRequest::METHOD_PATCH),
            array($this->getUri(), InternalRequest::METHOD_PATCH, $this->getHeaders()),
            array($this->getUri(), InternalRequest::METHOD_PATCH, $this->getHeaders(), $this->getData()),
            array(
                $this->getUri(),
                InternalRequest::METHOD_PATCH,
                $this->getHeaders(),
                $this->getData(),
                $this->getFiles(),
            ),
            array($this->getUri(), InternalRequest::METHOD_DELETE),
            array($this->getUri(), InternalRequest::METHOD_DELETE, $this->getHeaders()),
            array($this->getUri(), InternalRequest::METHOD_DELETE, $this->getHeaders(), $this->getData()),
            array(
                $this->getUri(),
                InternalRequest::METHOD_DELETE,
                $this->getHeaders(),
                $this->getData(),
                $this->getFiles(),
            ),
            array($this->getUri(), InternalRequest::METHOD_OPTIONS),
            array($this->getUri(), InternalRequest::METHOD_OPTIONS, $this->getHeaders()),
            array($this->getUri(), InternalRequest::METHOD_OPTIONS, $this->getHeaders(), $this->getData()),
            array(
                $this->getUri(),
                InternalRequest::METHOD_OPTIONS,
                $this->getHeaders(),
                $this->getData(),
                $this->getFiles(),
            ),
        );
    }

    /**
     * Gets the requests provider.
     *
     * @return array The requests provider.
     */
    public function requestsProvider()
    {
        $requests = array($this->getUri());

        foreach ($this->requestProvider() as $request) {
            $requests[] = array(
                $request[0],
                $request[1],
                InternalRequest::PROTOCOL_VERSION_1_1,
                isset($request[2]) ? $request[2] : array(),
                isset($request[3]) ? $request[3] : array(),
                isset($request[4]) ? $request[4] : array(),
            );
        }

        foreach ($this->requestProvider() as $request) {
            $requests[] = $this->httpAdapter->getConfiguration()->getMessageFactory()->createRequest(
                $request[0],
                $request[1],
                InternalRequest::PROTOCOL_VERSION_1_1,
                isset($request[2]) ? $request[2] : array(),
                http_build_query(isset($request[3]) ? $request[3] : array(), null, '&')
            );
        }

        foreach ($this->requestProvider() as $request) {
            $requests[] = $this->httpAdapter->getConfiguration()->getMessageFactory()->createInternalRequest(
                $request[0],
                $request[1],
                InternalRequest::PROTOCOL_VERSION_1_1,
                isset($request[2]) ? $request[2] : array(),
                isset($request[3]) ? $request[3] : array(),
                isset($request[4]) ? $request[4] : array()
            );
        }

        return $requests;
    }

    /**
     * Gets the errored requests provider.
     *
     * @return array The errored requests provider.
     */
    public function erroredRequestsProvider()
    {
        $requestsProvider = $this->requestsProvider();

        return array(
            $requestsProvider,
            array($this->getInvalidUri()),
        );
    }

    /**
     * Gets the timeout provider.
     *
     * @return array The timeout provider.
     */
    public function timeoutProvider()
    {
        return array(
            array(0.5),
            array(1),
        );
    }

    /**
     * Creates the http adapter.
     *
     * @return \Ivory\HttpAdapter\HttpAdapterInterface The created http adapter.
     */
    abstract protected function createHttpAdapter();

    /**
     * Asserts the response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface $response The response.
     * @param array                                        $options  The options.
     */
    protected function assertResponse($response, array $options = array())
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\ResponseInterface', $response);

        $options = array_merge($this->defaultOptions, $options);

        $this->assertSame($options['protocol_version'], $response->getProtocolVersion());
        $this->assertSame($options['status_code'], $response->getStatusCode());
        $this->assertSame($options['reason_phrase'], $response->getReasonPhrase());

        $this->assertNotEmpty($response->getHeaders());

        foreach ($options['headers'] as $name => $value) {
            $this->assertTrue($response->hasHeader($name));
            $this->assertStringStartsWith($value, $response->getHeaderLine($name));
        }

        if ($options['body'] === null) {
            $this->assertEmpty($response->getBody()->getContents());
            $this->assertEmpty((string) $response->getBody());
        } else {
            $this->assertContains($options['body'], $response->getBody()->getContents());
            $this->assertContains($options['body'], (string) $response->getBody());
        }

        $parameters = array();

        if (isset($options['redirect_count'])) {
            $parameters['redirect_count'] = $options['redirect_count'];
        }

        if (isset($options['effective_uri'])) {
            $parameters['effective_uri'] = $options['effective_uri'];
        }

        $this->assertArrayHasKey('duration', $response->getParameters());

        $params = $response->getParameters();
        unset($params['duration']);
        $this->assertSame($parameters, $params);
    }

    /**
     * Asserts the request.
     *
     * @param string $method          The method.
     * @param array  $headers         The headers.
     * @param array  $data            The data.
     * @param array  $files           The files.
     * @param string $protocolVersion The protocol version.
     */
    protected function assertRequest(
        $method,
        array $headers = array(),
        array $data = array(),
        array $files = array(),
        $protocolVersion = Request::PROTOCOL_VERSION_1_1
    ) {
        $request = $this->getRequest();

        $this->assertSame($protocolVersion, substr($request['SERVER']['SERVER_PROTOCOL'], 5));
        $this->assertSame($method, $request['SERVER']['REQUEST_METHOD']);

        $defaultHeaders = array(
            'Connection' => 'close',
            'User-Agent' => 'Ivory Http Adapter '.HttpAdapterInterface::VERSION,
        );

        $headers = array_merge($defaultHeaders, $headers);

        foreach ($headers as $name => $value) {
            if (is_int($name)) {
                list($name, $value) = explode(':', $value);
            }

            $name = strtoupper(str_replace(array('-'), array('_'), 'http-'.$name));

            $this->assertArrayHasKey($name, $request['SERVER']);
            $this->assertSame($value, $request['SERVER'][$name]);
        }

        $inputMethods = array(
            Request::METHOD_PUT,
            Request::METHOD_PATCH,
            Request::METHOD_DELETE,
            Request::METHOD_OPTIONS,
        );

        if (in_array($method, $inputMethods)) {
            $this->assertRequestInputData($request, $data, !empty($files));
            $this->assertRequestInputFiles($request, $files);
        } else {
            $this->assertRequestData($request, $data);
            $this->assertRequestFiles($request, $files);
        }
    }

    /**
     * Gets the uri.
     *
     * @param array $query The query.
     *
     * @return string|null The uri.
     */
    private function getUri(array $query = array())
    {
        return !empty($query) ? PHPUnitUtility::getUri().'?'.http_build_query($query, null, '&') : PHPUnitUtility::getUri();
    }

    /**
     * Gets the invalid uri.
     *
     * @return string The invalid uri.
     */
    private function getInvalidUri()
    {
        return 'http://invalid.egeloen.fr';
    }

    /**
     * Gets the client error uri.
     *
     * @return string The client error uri.
     */
    private function getClientErrorUri()
    {
        return $this->getUri(array('client_error' => true));
    }

    /**
     * Gets the server error uri.
     *
     * @return string The server error uri.
     */
    private function getServerErrorUri()
    {
        return $this->getUri(array('server_error' => true));
    }

    /**
     * Gets the delay uri.
     *
     * @param float $delay The delay.
     *
     * @return string The delay uri.
     */
    private function getDelayUri($delay = 1)
    {
        return $this->getUri(array('delay' => $delay + 0.01));
    }

    /**
     * Gets the redirect uri.
     *
     * @return string The redirect uri.
     */
    private function getRedirectUri()
    {
        return $this->getUri(array('redirect' => true));
    }

    /**
     * Gets the headers.
     *
     * @return array The headers.
     */
    private function getHeaders()
    {
        return array('Accept-Charset' => 'utf-8', 'Accept-Language:fr');
    }

    /**
     * Gets the data.
     *
     * @return array The data.
     */
    private function getData()
    {
        return array('param1' => 'foo', 'param2' => array('bar', array('baz')));
    }

    /**
     * Gets the files.
     *
     * @return array The files.
     */
    private function getFiles()
    {
        return array(
            'file1' => realpath(__DIR__.'/Fixtures/files/file1.txt'),
            'file2' => array(
                realpath(__DIR__.'/Fixtures/files/file2.txt'),
                array(realpath(__DIR__.'/Fixtures/files/file3.txt')),
            ),
        );
    }

    /**
     * Asserts the request data.
     *
     * @param array $request The request.
     * @param array $data    The data.
     */
    private function assertRequestData(array $request, array $data)
    {
        foreach ($data as $name => $value) {
            $this->assertArrayHasKey($name, $request['POST']);
            $this->assertSame($value, $request['POST'][$name]);
        }
    }

    /**
     * Asserts the request input data.
     *
     * @param array   $request   The request.
     * @param array   $data      The data.
     * @param boolean $multipart TRUE if the input data is multipart else FALSE.
     */
    private function assertRequestInputData(array $request, array $data, $multipart)
    {
        if ($multipart) {
            foreach ($data as $name => $value) {
                $this->assertRequestMultipartData($request, $name, $value);
            }
        } else {
            parse_str($request['INPUT'], $request['POST']);
            $this->assertRequestData($request, $data);
        }
    }

    /**
     * Asserts the request multipart data.
     *
     * @param array        $request The request.
     * @param string       $name    The name.
     * @param array|string $data    The data.
     */
    private function assertRequestMultipartData(array $request, $name, $data)
    {
        if (is_array($data)) {
            foreach ($data as $subName => $subValue) {
                $this->assertRequestMultipartData($request, $name.'['.$subName.']', $subValue);
            }
        } else {
            $pattern = '/Content-Disposition: form-data; name="'.preg_quote($name).'"\s+'.preg_quote($data).'/';
            $this->assertRegExp($pattern, $request['INPUT']);
        }
    }

    /**
     * Asserts the request files.
     *
     * @param array $request The request.
     * @param array $files   The files.
     */
    private function assertRequestFiles(array $request, array $files)
    {
        foreach ($files as $name => $file) {
            $this->assertRequestFile($request, $name, $file);
        }
    }

    /**
     * Asserts the request file.
     *
     * @param array  $request The request.
     * @param string $name    The name.
     * @param string $file    The file.
     */
    private function assertRequestFile(array $request, $name, $file)
    {
        if (is_array($file)) {
            foreach ($file as $subName => $subFile) {
                $this->assertRequestFile($request, $name.'['.$subName.']', $subFile);
            }
        } else {
            if (!preg_match('/^([^\[]+)/', $name, $nameMatches)) {
                $this->fail();
            }

            $this->assertArrayHasKey($nameMatches[1], $request['FILES']);

            $fileRequest = $request['FILES'][$nameMatches[1]];
            $fileName = basename($file);
            $fileSize = strlen(file_get_contents($file));
            $levels = preg_match_all('/\[(\d+)\]/', $name, $indexMatches) ? $indexMatches[1] : array();

            $this->assertRequestPropertyFile($fileName, 'name', $fileRequest, $levels);
            $this->assertRequestPropertyFile($fileSize, 'size', $fileRequest, $levels);
            $this->assertRequestPropertyFile(0, 'error', $fileRequest, $levels);
        }
    }

    /**
     * Asserts the request property file.
     *
     * @param mixed  $expected The expected.
     * @param string $property The property.
     * @param array  $file     The file.
     * @param array  $levels   The levels.
     */
    private function assertRequestPropertyFile($expected, $property, array $file, array $levels = array())
    {
        if (!empty($levels)) {
            $this->assertRequestPropertyFile($expected, $levels[0], $file[$property], array_slice($levels, 1));
        } else {
            $this->assertSame($expected, $file[$property]);
        }
    }

    /**
     * Asserts the request input files.
     *
     * @param array $request The request.
     * @param array $files   The files.
     */
    private function assertRequestInputFiles(array $request, array $files)
    {
        foreach ($files as $name => $file) {
            $this->assertRequestInputFile($request, $name, $file);
        }
    }

    /**
     * Asserts the request input file.
     *
     * @param array        $request The request.
     * @param string       $name    The name.
     * @param array|string $file    The file.
     */
    private function assertRequestInputFile(array $request, $name, $file)
    {
        if (is_array($file)) {
            foreach ($file as $subName => $subFile) {
                $this->assertRequestInputFile($request, $name.'['.$subName.']', $subFile);
            }
        } else {
            $namePattern = '; name="'.preg_quote($name).'"';
            $filenamePattern = '; filename=".*'.preg_quote(basename($file)).'"';

            $subPattern = '('.$namePattern.$filenamePattern.'|'.$filenamePattern.$namePattern.')';
            $pattern = '/Content-Disposition: form-data'.$subPattern.'.*'.preg_quote(file_get_contents($file)).'/sm';

            $this->assertRegExp($pattern, $request['INPUT']);
        }
    }

    /**
     * Asserts the multi responses.
     *
     * @param array $responses The responses.
     * @param array $requests  The requests.
     */
    private function assertMultiResponses(array $responses, array $requests)
    {
        $this->assertCount(count($requests), $responses);

        foreach ($responses as $response) {
            $this->assertTrue($response->hasParameter('request'));
            $this->assertInstanceOf(
                'Ivory\HttpAdapter\Message\InternalRequestInterface',
                $response->getParameter('request')
            );
        }
    }

    /**
     * Asserts the multi exceptions.
     *
     * @param array $exceptions The exceptions.
     * @param array $requests   The requests.
     */
    private function assertMultiExceptions(array $exceptions, array $requests)
    {
        $this->assertCount(count($requests), $exceptions);

        foreach ($exceptions as $exception) {
            $this->assertTrue($exception->hasRequest());
            $this->assertInstanceOf(
                'Ivory\HttpAdapter\Message\InternalRequestInterface',
                $exception->getRequest()
            );
        }
    }

    /**
     * Gets the request.
     *
     * @return array The request.
     */
    private function getRequest()
    {
        $file = fopen(self::$file, 'r');
        flock($file, LOCK_EX);
        $request = json_decode(stream_get_contents($file), true);
        flock($file, LOCK_UN);
        fclose($file);

        return $request;
    }
}
