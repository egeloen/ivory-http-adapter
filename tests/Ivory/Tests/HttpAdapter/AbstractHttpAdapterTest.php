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

use Ivory\HttpAdapter\HttpAdapterConfigInterface;
use Ivory\HttpAdapter\Message\Request;
use Ivory\HttpAdapter\Message\Stream\StringStream;
use Ivory\Tests\HttpAdapter\Utils\PHPUnitUtility;

/**
 * Abstract http adapter test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractHttpAdapterTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\HttpAdapter\HttpAdapterInterface */
    protected $httpAdapter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!$this->getUrl()) {
            $this->markTestSkipped();
        }

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
     */
    public function testGet($url, array $headers = array())
    {
        $this->assertResponse($this->httpAdapter->get($url, $headers));
        $this->assertRequest(Request::METHOD_GET, $headers);
    }

    /**
     * @dataProvider simpleProvider
     */
    public function testHead($url, array $headers = array())
    {
        $this->assertResponse($this->httpAdapter->head($url, $headers), array('body' => null));
        $this->assertRequest(Request::METHOD_HEAD, $headers);
    }

    /**
     * @dataProvider fullProvider
     */
    public function testPost($url, array $headers = array(), array $data = array(), array $files = array())
    {
        $this->assertResponse($this->httpAdapter->post($url, $headers, $data, $files));
        $this->assertRequest(Request::METHOD_POST, $headers, $data, $files);
    }

    /**
     * @dataProvider fullProvider
     */
    public function testPut($url, array $headers = array(), array $data = array(), array $files = array())
    {
        $this->assertResponse($this->httpAdapter->put($url, $headers, $data, $files));
        $this->assertRequest(Request::METHOD_PUT, $headers, $data, $files);
    }

    /**
     * @dataProvider fullProvider
     */
    public function testPatch($url, array $headers = array(), array $data = array(), array $files = array())
    {
        $this->assertResponse($this->httpAdapter->patch($url, $headers, $data, $files));
        $this->assertRequest(Request::METHOD_PATCH, $headers, $data, $files);
    }

    /**
     * @dataProvider fullProvider
     */
    public function testDelete($url, array $headers = array(), array $data = array(), array $files = array())
    {
        $this->assertResponse($this->httpAdapter->delete($url, $headers, $data, $files));
        $this->assertRequest(Request::METHOD_DELETE, $headers, $data, $files);
    }

    /**
     * @dataProvider fullProvider
     */
    public function testOptions($url, array $headers = array(), array $data = array(), array $files = array())
    {
        $this->assertResponse($this->httpAdapter->options($url, $headers, $data, $files));
        $this->assertRequest(Request::METHOD_OPTIONS, $headers, $data, $files);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testSendRequest($url, $method, array $headers = array(), array $data = array())
    {
        $request = new Request($url, $method);
        $request->setHeaders($headers);
        $request->setBody(new StringStream(http_build_query($data)));

        $options = array();
        if ($method === Request::METHOD_HEAD) {
            $options['body'] = null;
        }

        $this->assertResponse($this->httpAdapter->sendRequest($request), $options);
        $this->assertRequest($method, $headers, $data);
    }

    public function testSendWithProtocolVersion10()
    {
        $this->httpAdapter->setProtocolVersion($protocolVersion = Request::PROTOCOL_VERSION_10);

        $this->assertResponse($this->httpAdapter->send($this->getUrl(), $method = Request::METHOD_GET));
        $this->assertRequest($method, array(), array(), array(), $protocolVersion);
    }

    public function testSendWithExplicitEncodingType()
    {
        $this->httpAdapter->setEncodingType(HttpAdapterConfigInterface::ENCODING_TYPE_URLENCODED);

        $url = $this->getUrl();
        $method = Request::METHOD_POST;
        $headers = $this->getHeaders();
        $data = $this->getData();

        $this->assertResponse($this->httpAdapter->send($url, $method, $headers, $data));
        $this->assertRequest($method, $headers, $data);
    }

    /**
     * @dataProvider timeoutProvider
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSendWithTimeoutExceeded($timeout)
    {
        $this->httpAdapter->setTimeout($timeout);
        $this->httpAdapter->send($this->getDelayUrl($timeout), Request::METHOD_GET);
    }

    public function testSendWithSingleRedirect()
    {
        $this->assertResponse(
            $this->httpAdapter->send($this->getRedirectUrl(), $method = Request::METHOD_GET),
            array('effective_url' => $this->getUrl())
        );

        $this->assertRequest($method);
    }

    public function testSendWithMultipleRedirects()
    {
        $this->assertResponse(
            $this->httpAdapter->send(
                $this->getRedirectUrl($this->httpAdapter->getMaxRedirects()),
                $method = Request::METHOD_GET
            ),
            array('effective_url' => $this->getUrl())
        );

        $this->assertRequest($method);
    }

    public function testSendWithRedirectDisabled()
    {
        $this->httpAdapter->setMaxRedirects(0);

        $this->assertResponse(
            $this->httpAdapter->send($url = $this->getRedirectUrl(), $method = Request::METHOD_GET),
            array(
                'status_code'   => 302,
                'reason_phrase' => 'Moved Temporarily',
                'body'          => 'Redirect: 1',
                'effective_url' => $this->getRedirectUrl(),
            )
        );

        $this->assertRequest($method);
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSendWithMaxRedirectsExceeded()
    {
        $this->httpAdapter->setMaxRedirects(1);

        $this->httpAdapter->send($this->getRedirectUrl(2), Request::METHOD_GET);
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSendWithInvalidUrl()
    {
        $this->httpAdapter->send('http://invalid.egeloen.fr', Request::METHOD_GET);
    }

    /**
     * @expectedException \Ivory\HttpAdapter\HttpAdapterException
     */
    public function testSendWithDataAsStringAndFiles()
    {
        $this->httpAdapter->send(
            $this->getUrl(),
            Request::METHOD_POST,
            array(),
            http_build_query($this->getData()),
            $this->getFiles()
        );
    }

    /**
     * Gets the simple provider.
     *
     * @return array The simple provider.
     */
    public function simpleProvider()
    {
        return array(
            array($this->getUrl()),
            array($this->getUrl(), $this->getHeaders()),
        );
    }

    /**
     * Gets the full provider.
     *
     * @return array The full provider.
     */
    public function fullProvider()
    {
        return array(
            array($this->getUrl()),
            array($this->getUrl(), $this->getHeaders()),
            array($this->getUrl(), $this->getHeaders(), $this->getData()),
            array($this->getUrl(), $this->getHeaders(), $this->getData(), $this->getFiles()),
        );
    }

    /**
     * Gets the request provider.
     *
     * @return array The request provider.
     */
    public function requestProvider()
    {
        return array(
            array($this->getUrl(), Request::METHOD_GET),
            array($this->getUrl(), Request::METHOD_GET, $this->getHeaders()),
            array($this->getUrl(), Request::METHOD_HEAD),
            array($this->getUrl(), Request::METHOD_HEAD, $this->getHeaders()),
            array($this->getUrl(), Request::METHOD_POST),
            array($this->getUrl(), Request::METHOD_POST, $this->getHeaders()),
            array($this->getUrl(), Request::METHOD_POST, $this->getHeaders(), $this->getData()),
            array($this->getUrl(), Request::METHOD_PUT),
            array($this->getUrl(), Request::METHOD_PUT, $this->getHeaders()),
            array($this->getUrl(), Request::METHOD_PUT, $this->getHeaders(), $this->getData()),
            array($this->getUrl(), Request::METHOD_PATCH),
            array($this->getUrl(), Request::METHOD_PATCH, $this->getHeaders()),
            array($this->getUrl(), Request::METHOD_PATCH, $this->getHeaders(), $this->getData()),
            array($this->getUrl(), Request::METHOD_DELETE),
            array($this->getUrl(), Request::METHOD_DELETE, $this->getHeaders()),
            array($this->getUrl(), Request::METHOD_DELETE, $this->getHeaders(), $this->getData()),
            array($this->getUrl(), Request::METHOD_OPTIONS),
            array($this->getUrl(), Request::METHOD_OPTIONS, $this->getHeaders()),
            array($this->getUrl(), Request::METHOD_OPTIONS, $this->getHeaders(), $this->getData()),
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
     * Gets the url.
     *
     * @return string|null The url.
     */
    protected function getUrl()
    {
        return PHPUnitUtility::getUrl();
    }

    /**
     * Gets the delay url.
     *
     * @param float $delay The delay.
     *
     * @return string The delay url.
     */
    protected function getDelayUrl($delay = 1)
    {
        return $this->getUrl().'?'.http_build_query(array('delay' => $delay + 0.1));
    }

    /**
     * Gets the redirect url.
     *
     * @param integer $redirectCount The redirect count.
     *
     * @return string The redirect url.
     */
    protected function getRedirectUrl($redirectCount = 1)
    {
        return $this->getUrl().'?'.http_build_query(array('redirect' => $redirectCount));
    }

    /**
     * Gets the headers.
     *
     * @return array The headers.
     */
    protected function getHeaders()
    {
        return array('Accept-Charset' => 'utf-8', 'Accept-Encoding:gzip');
    }

    /**
     * Gets the data.
     *
     * @return array The data.
     */
    protected function getData()
    {
        return array('param1' => 'foo', 'param2[0]' => 'bar', 'param2[1]' => 'baz');
    }

    /**
     * Gets the files.
     *
     * @return array The files.
     */
    protected function getFiles()
    {
        return array(
            'file1'    => realpath(__DIR__.'/Fixtures/files/file1.txt'),
            'file2[0]' => realpath(__DIR__.'/Fixtures/files/file2.txt'),
            'file2[1]' => realpath(__DIR__.'/Fixtures/files/file3.txt'),
        );
    }

    /**
     * Asserts the response.
     *
     * @param \Ivory\HttpAdapter\Message\ResponseInterface $response The response.
     * @param array                                        $options  The options.
     */
    protected function assertResponse($response, array $options = array())
    {
        $this->assertInstanceOf('Ivory\HttpAdapter\Message\ResponseInterface', $response);

        $options = array_merge(
            array(
                'protocol_version' => Request::PROTOCOL_VERSION_11,
                'status_code'      => 200,
                'reason_phrase'    => 'OK',
                'headers'          => array('content-type' => 'text/html'),
                'body'             => 'Ok',
                'effective_url'    => $this->getUrl(),
            ),
            $options
        );

        $this->assertSame($options['protocol_version'], $response->getProtocolVersion());
        $this->assertSame($options['status_code'], $response->getStatusCode());
        $this->assertSame($options['reason_phrase'], $response->getReasonPhrase());

        $this->assertNotEmpty($response->getHeaders());

        foreach ($options['headers'] as $name => $value) {
            $this->assertTrue($response->hasHeader($name));
            $this->assertStringStartsWith($value, $response->getHeader($name));
        }

        if ($options['body'] === null) {
            $this->assertFalse($response->hasBody());
        } else {
            $this->assertSame($options['body'], (string) $response->getBody());
        }

        $this->assertSame($options['effective_url'], $response->getEffectiveUrl());
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
        $protocolVersion = Request::PROTOCOL_VERSION_11
    ) {
        $request = $this->getRequest();

        $this->assertSame($protocolVersion, substr($request['SERVER']['SERVER_PROTOCOL'], 5));
        $this->assertSame($method, $request['SERVER']['REQUEST_METHOD']);

        foreach ($headers as $name => $value) {
            if (is_int($name)) {
                $explode = explode(':', $value);
                $name = $explode[0];
                $value = $explode[1];
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
     * Asserts the request data.
     *
     * @param array $request The request.
     * @param array $data    The data.
     */
    protected function assertRequestData(array $request, array $data)
    {
        foreach ($data as $name => $value) {
            if (!preg_match('/^([^\[\]]+)(\[(\d+)\])?$/', $name, $matches)) {
                $this->fail();
            }

            $this->assertArrayHasKey($matches[1], $request['POST']);

            if (isset($matches[3])) {
                $this->assertArrayHasKey($matches[3], $request['POST'][$matches[1]]);
                $this->assertSame($value, $request['POST'][$matches[1]][$matches[3]]);
            } else {
                $this->assertSame($value, $request['POST'][$matches[1]]);
            }
        }
    }

    /**
     * Asserts the request input data.
     *
     * @param array   $request   The request.
     * @param array   $data      The data.
     * @param boolean $multipart TRUE if the input data is multipart else FALSE.
     */
    protected function assertRequestInputData(array $request, array $data, $multipart)
    {
        if ($multipart) {
            foreach ($data as $name => $value) {
                $pattern = '/Content-Disposition: form-data; name="'.preg_quote($name).'"\s+'.preg_quote($value).'/';
                $this->assertRegExp($pattern, $request['INPUT']);
            }
        } else {
            parse_str($request['INPUT'], $request['POST']);
            $this->assertRequestData($request, $data);
        }
    }

    /**
     * Asserts the request files.
     *
     * @param array $request The request.
     * @param array $files   The files.
     */
    protected function assertRequestFiles(array $request, array $files)
    {
        foreach ($files as $name => $file) {
            if (!preg_match('/^([^\[\]]+)(\[(\d+)\])?$/', $name, $matches)) {
                $this->fail();
            }

            $this->assertArrayHasKey($matches[1], $request['FILES']);

            $fileName = basename($file);
            $fileSize = strlen(file_get_contents($file));

            if (isset($matches[3])) {
                $this->assertArrayHasKey($matches[3], $request['FILES'][$matches[1]]['tmp_name']);

                $this->assertSame($fileName, $request['FILES'][$matches[1]]['name'][$matches[3]]);
                $this->assertSame($fileSize, $request['FILES'][$matches[1]]['size'][$matches[3]]);
                $this->assertNotEmpty($request['FILES'][$matches[1]]['tmp_name'][$matches[3]]);
                $this->assertSame(0, $request['FILES'][$matches[1]]['error'][$matches[3]]);
            } else {
                $this->assertSame($fileName, $request['FILES'][$matches[1]]['name']);
                $this->assertSame($fileSize, $request['FILES'][$matches[1]]['size']);
                $this->assertNotEmpty($request['FILES'][$matches[1]]['tmp_name']);
                $this->assertSame(0, $request['FILES'][$matches[1]]['error']);
            }
        }
    }

    /**
     * Asserts the request input files.
     *
     * @param array $request The request.
     * @param array $files   The files.
     */
    protected function assertRequestInputFiles(array $request, array $files)
    {
        foreach ($files as $name => $file) {
            $namePattern =  '; name="'.preg_quote($name).'"';
            $filenamePattern = '; filename=".*'.preg_quote(basename($file)).'"';

            $subPattern = '('.$namePattern.$filenamePattern.'|'.$filenamePattern.$namePattern.')';
            $pattern = '/Content-Disposition: form-data'.$subPattern.'.*'.preg_quote(file_get_contents($file)).'/sm';

            $this->assertRegExp($pattern, $request['INPUT']);
        }
    }

    /**
     * Gets the request.
     *
     * @return array The request.
     */
    protected function getRequest()
    {
        usleep(50000);

        return json_decode(file_get_contents(realpath(sys_get_temp_dir()).'/http-adapter.log'), true);
    }
}
