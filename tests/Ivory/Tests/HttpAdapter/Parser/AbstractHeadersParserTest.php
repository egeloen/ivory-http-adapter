<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Parser;

/**
 * Abstract parser test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractHeadersParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Gets the headers provider.
     *
     * @return array The headers provider.
     */
    public function headersProvider()
    {
        return array_merge($this->simpleHeadersProvider(), $this->redirectHeadersProvider());
    }

    /**
     * Gets the simple headers provider.
     *
     * @return array The simple headers provider.
     */
    public function simpleHeadersProvider()
    {
        return array(
            array($this->getStringHeaders()),
            array($this->getIndexedHeaders()),
            array($this->getAssociativeHeaders()),
            array($this->getSubAssociativeHeaders()),
        );
    }

    /**
     * Gets the redirect headers provider.
     *
     * @return array The redirect headers provider.
     */
    public function redirectHeadersProvider()
    {
        return array(
            array($this->getRedirectStringHeaders()),
            array($this->getRedirectIndexedHeaders()),
            array($this->getRedirectAssociativeHeaders()),
            array($this->getRedirectSubAssociativeHeaders()),
        );
    }

    /**
     * Gets the string headers.
     *
     * @return string The string headers.
     */
    protected function getStringHeaders()
    {
        return implode("\r\n", $this->getIndexedHeaders());
    }

    /**
     * Gets the indexed headers.
     *
     * @return array The indexed headers.
     */
    protected function getIndexedHeaders()
    {
        $headers = array();

        foreach ($this->getAssociativeHeaders() as $name => $value) {
            $headers[] = is_int($name) ? $value : $name.': '.$value;
        }

        return $headers;
    }

    /**
     * Gets the associative headers.
     *
     * @return array The associative headers.
     */
    protected function getAssociativeHeaders()
    {
        $headers = array();

        foreach ($this->getSubAssociativeHeaders() as $name => $value) {
            if (is_int($name)) {
                $headers[] = $value;
            } else {
                $headers[$name] = implode(', ', $value);
            }
        }

        return $headers;
    }

    /**
     * Gets the sub associative headers.
     *
     * @return array The sub associative headers.
     */
    protected function getSubAssociativeHeaders()
    {
        return array(
            'HTTP/1.1 200 OK',
            'foo' => array('bar'),
            'baz' => array('bat', 'ban'),
        );
    }

    /**
     * Gets the redirect string headers.
     *
     * @return string The redirect string headers.
     */
    protected function getRedirectStringHeaders()
    {
        return implode("\r\n", $this->getRedirectIndexedHeaders());
    }

    /**
     * Gets the redirect indexed headers.
     *
     * @return array The redirect indexed headers.
     */
    protected function getRedirectIndexedHeaders()
    {
        $headers = array();

        foreach ($this->getRedirectAssociativeHeaders() as $name => $value) {
            $headers[] = is_int($name) ? $value : $name.': '.$value;
        }

        return $headers;
    }

    /**
     * Gets the redirect associative headers.
     *
     * @return array The redirect associative headers.
     */
    protected function getRedirectAssociativeHeaders()
    {
        $headers = array();

        foreach ($this->getRedirectSubAssociativeHeaders() as $name => $value) {
            if (is_int($name)) {
                $headers[] = $value;
            } else {
                $headers[$name] = implode(', ', $value);
            }
        }

        return $headers;
    }

    /**
     * Gets the redirect sub associative headers.
     *
     * @return array The redirect sub associative headers.
     */
    protected function getRedirectSubAssociativeHeaders()
    {
        return array_merge(
            array(
                'HTTP/1.0 302 Temporary moved',
                'bin'      => array('bot'),
                'location' => array($this->getRedirectLocation()),
                '',
            ),
            $this->getSubAssociativeHeaders()
        );
    }

    /**
     * Gets the redirect location.
     *
     * @return string The redirect location.
     */
    protected function getRedirectLocation()
    {
        return 'http://egeloen.fr/';
    }
}
