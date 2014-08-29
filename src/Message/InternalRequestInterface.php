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
     * Clears the raw datas.
     *
     * @return void No return value.
     */
    public function clearRawDatas();

    /**
     * Checks if there are raw datas.
     *
     * @return boolean TRUE if there are raw datas else FALSE.
     */
    public function hasRawDatas();

    /**
     * Gets the raw datas.
     *
     * @return string The raw datas.
     */
    public function getRawDatas();

    /**
     * Sets the raw datas.
     *
     * @param string $rawDatas The raw datas.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the are datas.
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the are files.
     *
     * @return void No return value.
     */
    public function setRawDatas($rawDatas);

    /**
     * Clears the datas.
     *
     * @return void No return value.
     */
    public function clearDatas();

    /**
     * Checks if there are datas.
     *
     * @return boolean TRUE if there are datas else FALSE.
     */
    public function hasDatas();

    /**
     * Gets the datas.
     *
     * @return array The datas.
     */
    public function getDatas();

    /**
     * Sets the datas.
     *
     * @param array $datas The datas.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the are raw datas.
     *
     * @return void No return value.
     */
    public function setDatas(array $datas);

    /**
     * Adds the datas.
     *
     * @param array $datas The datas.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the are raw datas.
     *
     * @return void No return value.
     */
    public function addDatas(array $datas);

    /**
     * Removes the datas.
     *
     * @param array $names The data names.
     *
     * @return void No return value.
     */
    public function removeDatas(array $names);

    /**
     * Checks if there is the data.
     *
     * @param string $name The data name.
     *
     * @return boolean TRUE if there is the data else FALSE.
     */
    public function hasData($name);

    /**
     * Gets the data.
     *
     * @param string $name The data name.
     *
     * @return mixed The data value.
     */
    public function getData($name);

    /**
     * Sets the data.
     *
     * @param string $name  The data name.
     * @param mixed  $value The data value.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the are raw datas.
     *
     * @return void No return value.
     */
    public function setData($name, $value);

    /**
     * Adds the data.
     *
     * @param string $name  The data name.
     * @param mixed  $value The data value.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the are raw datas.
     *
     * @return void No return value.
     */
    public function addData($name, $value);

    /**
     * Removes a data.
     *
     * @param string $name The data name.
     *
     * @return void No return value.
     */
    public function removeData($name);

    /**
     * Clears the files.
     *
     * @return void No return value.
     */
    public function clearFiles();

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
     * @param array $files The files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the are raw datas.
     *
     * @return void No return value.
     */
    public function setFiles(array $files);

    /**
     * Adds the files.
     *
     * @param array $files The files.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the are raw datas.
     *
     * @return void No return value.
     */
    public function addFiles(array $files);

    /**
     * Removes the files.
     *
     * @param array $names The file names.
     *
     * @return void No return value.
     */
    public function removeFiles(array $names);

    /**
     * Checks if there is the file.
     *
     * @param string $name The file name.
     *
     * @return boolean TRUE if there is the file else FALSE.
     */
    public function hasFile($name);

    /**
     * Gets a file.
     *
     * @param string $name The file name.
     *
     * @return string The file.
     */
    public function getFile($name);

    /**
     * Sets a file.
     *
     * @param string $name The file name.
     * @param string $file The file.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the are raw datas.
     *
     * @return void No return value.
     */
    public function setFile($name, $file);

    /**
     * Adds a file.
     *
     * @param string $name The file name.
     * @param string $file The file.
     *
     * @throws \Ivory\HttpAdapter\HttpAdapterException If the are raw datas.
     *
     * @return void No return value.
     */
    public function addFile($name, $file);

    /**
     * Removes a file.
     *
     * @param string $name The file name.
     *
     * @return void No return value.
     */
    public function removeFile($name);
}
