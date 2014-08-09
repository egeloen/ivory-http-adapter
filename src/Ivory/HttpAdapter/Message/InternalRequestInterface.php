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
 * Internal request interface.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface InternalRequestInterface extends RequestInterface
{
    /**
     * Checks if there is a data.
     *
     * @return boolean TRUE if there is a data else FALSE.
     */
    public function hasData();

    /**
     * Checks if there is a string data.
     *
     * @return boolean TRUE if there is a string data else FALSE.
     */
    public function hasStringData();

    /**
     * Checks if there is an array data.
     *
     * @return boolean TRUE if there is an array data else FALSE.
     */
    public function hasArrayData();

    /**
     * Gets the data.
     *
     * @return array|string The data.
     */
    public function getData();

    /**
     * Sets the data.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the data is a string and there are files.
     *
     * @param array|string $data The data.
     */
    public function setData($data);

    /**
     * Checks if there are files.
     *
     * @return boolean TRUE if there are files else FALSE.
     */
    public function hasFiles();

    /**
     * Gets the files.
     *
     * @return array The files.
     */
    public function getFiles();

    /**
     * Sets the files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If there is data as string and there are files.
     *
     * @param array $files The files.
     */
    public function setFiles(array $files);
}
