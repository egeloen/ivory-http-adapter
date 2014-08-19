<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

$delay = isset($_GET['delay']) ? $_GET['delay'] : 0;
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : false;

if ($delay > 0) {
    usleep($delay * 1000000);
}

if ($redirect) {
    header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']);
    echo 'Redirect';
} else {
    echo 'Ok';
}

file_put_contents(
    realpath(sys_get_temp_dir()).'/http-adapter.log',
    json_encode(array(
        'SERVER' => $_SERVER,
        'GET'    => $_GET,
        'POST'   => $_POST,
        'FILES'  => $_FILES,
        'INPUT'  => file_get_contents('php://input'),
    ))
);
