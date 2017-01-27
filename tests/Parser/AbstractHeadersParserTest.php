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

use Ivory\Tests\HttpAdapter\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractHeadersParserTest extends AbstractTestCase
{
    /**
     * @return array
     */
    public function headersProvider()
    {
        return array_merge($this->simpleHeadersProvider(), $this->redirectHeadersProvider());
    }

    /**
     * @return array
     */
    public function simpleHeadersProvider()
    {
        return [
            [$this->getStringHeaders()],
            [$this->getIndexedHeaders()],
            [$this->getAssociativeHeaders()],
            [$this->getSubAssociativeHeaders()],
        ];
    }

    /**
     * @return array
     */
    public function redirectHeadersProvider()
    {
        return [
            [$this->getRedirectStringHeaders()],
            [$this->getRedirectIndexedHeaders()],
            [$this->getRedirectAssociativeHeaders()],
            [$this->getRedirectSubAssociativeHeaders()],
        ];
    }

    /**
     * @return string
     */
    private function getStringHeaders()
    {
        return implode("\r\n", $this->getIndexedHeaders());
    }

    /**
     * @return array
     */
    private function getIndexedHeaders()
    {
        $headers = [];

        foreach ($this->getAssociativeHeaders() as $name => $value) {
            $headers[] = is_int($name) ? $value : $name.': '.$value;
        }

        return $headers;
    }

    /**
     * @return array
     */
    private function getAssociativeHeaders()
    {
        $headers = [];

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
     * @return array
     */
    private function getSubAssociativeHeaders()
    {
        return [
            'HTTP/1.1 200 OK',
            'foo' => ['bar'],
            'baz' => ['bat', 'ban'],
        ];
    }

    /**
     * @return string
     */
    private function getRedirectStringHeaders()
    {
        return implode("\r\n", $this->getRedirectIndexedHeaders());
    }

    /**
     * @return array
     */
    private function getRedirectIndexedHeaders()
    {
        $headers = [];

        foreach ($this->getRedirectAssociativeHeaders() as $name => $value) {
            $headers[] = is_int($name) ? $value : $name.': '.$value;
        }

        return $headers;
    }

    /**
     * @return array
     */
    private function getRedirectAssociativeHeaders()
    {
        $headers = [];

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
     * @return array
     */
    private function getRedirectSubAssociativeHeaders()
    {
        return array_merge(
            [
                'HTTP/1.0 302 Temporary moved',
                'bin'      => ['bot'],
                'location' => [$this->getRedirectLocation()],
                '',
            ],
            $this->getSubAssociativeHeaders()
        );
    }

    /**
     * @return string
     */
    private function getRedirectLocation()
    {
        return 'http://egeloen.fr/';
    }
}
