<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\Tests\HttpAdapter\Normalizer;

use Ivory\HttpAdapter\Normalizer\HeadersNormalizer;

/**
 * Headers normalizer test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class HeadersNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalizeAsAssociativeWithStringHeaders()
    {
        $this->assertSame(
            array('fOo' => 'bar, bot', 'Baz' => 'bat, ban', 'Date' => 'Fri, 15 aug 2014 12:34:56 UTC'),
            HeadersNormalizer::normalize($this->getStringHeaders())
        );
    }

    public function testNormalizeAsNotAssociativeWithStringHeaders()
    {
        $this->assertSame(
            array('fOo: bar, bot', 'Baz: bat, ban', 'Date: Fri, 15 aug 2014 12:34:56 UTC'),
            HeadersNormalizer::normalize($this->getStringHeaders(), false)
        );
    }

    public function testNormalizeAsAssociativeWithSubAssociativeArrayHeaders()
    {
        $this->assertSame(
            array('fOo' => 'bar, bot', 'Baz' => 'bat, ban', 'Date' => 'Fri, 15 aug 2014 12:34:56 UTC'),
            HeadersNormalizer::normalize($this->getSubAssociativeHeaders())
        );
    }

    public function testNormalizeAsAssociativeWithAssociativeArrayHeaders()
    {
        $this->assertSame(
            array('fOo' => 'bar, bot', 'Baz' => 'bat, ban', 'Date' => 'Fri, 15 aug 2014 12:34:56 UTC'),
            HeadersNormalizer::normalize($this->getAssociativeHeaders())
        );
    }

    public function testNormalizeAsNotAssociativeWithIndexedArrayHeaders()
    {
        $this->assertSame(
            array('fOo: bar, bot', 'Baz: bat, ban', 'Date: Fri, 15 aug 2014 12:34:56 UTC'),
            HeadersNormalizer::normalize($this->getIndexedHeaders(), false)
        );
    }

    public function testNormalizeHeaderName()
    {
        $this->assertSame('FoO', HeadersNormalizer::normalizeHeaderName(' FoO '));
    }

    public function testNormalizeHeaderValueWithString()
    {
        $this->assertSame('foo, bar', HeadersNormalizer::normalizeHeaderValue(' foo , bar '));
    }

    public function testNormalizeHeaderValueWithArray()
    {
        $this->assertSame('foo, bar', HeadersNormalizer::normalizeHeaderValue(array(' foo ', ' bar ')));
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
            ' fOo '  => array(' bar '),
            'fOo'    => array(' bot '),
            ' Baz '  => array(' bat ', ' ban '),
            ' Date ' => array(' Fri, 15 aug 2014 12:34:56 UTC ')
        );
    }
}
