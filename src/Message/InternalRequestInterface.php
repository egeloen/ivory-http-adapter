<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\HttpAdapter\Message;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface InternalRequestInterface extends RequestInterface
{
    /**
     * @return array
     */
    public function getDatas();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasData($name);

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getData($name);

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return InternalRequestInterface
     */
    public function withData($name, $value);

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return InternalRequestInterface
     */
    public function withAddedData($name, $value);

    /**
     * @param string $name
     *
     * @return InternalRequestInterface
     */
    public function withoutData($name);

    /**
     * @return array
     */
    public function getFiles();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasFile($name);

    /**
     * @param string $name
     *
     * @return string
     */
    public function getFile($name);

    /**
     * @param string $name
     * @param string $file
     *
     * @return InternalRequestInterface
     */
    public function withFile($name, $file);

    /**
     * @param string $name
     * @param string $file
     *
     * @return InternalRequestInterface
     */
    public function withAddedFile($name, $file);

    /**
     * @param string $name
     *
     * @return InternalRequestInterface
     */
    public function withoutFile($name);
}
