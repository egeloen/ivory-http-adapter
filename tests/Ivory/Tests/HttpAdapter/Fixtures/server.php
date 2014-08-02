<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 0;

if ($redirect > 0) {
    $query = ($redirect - 1) > 0 ? '?'.http_build_query(array('redirect' => $redirect - 1)) : '';
    header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].$query);

    echo 'Redirect: '.$redirect;
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
