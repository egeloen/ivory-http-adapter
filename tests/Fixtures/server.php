<?php

/*
 * This file is part of the Ivory Http Adapter package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../Utility/PHPUnitUtility.php';

use Ivory\Tests\HttpAdapter\Utility\PHPUnitUtility;

$file = fopen(PHPUnitUtility::getFile(true, 'http-adapter.log'), 'c');
flock($file, LOCK_EX);
ftruncate($file, 0);

$serverError = isset($_GET['server_error']) ? $_GET['server_error'] : false;
$clientError = isset($_GET['client_error']) ? $_GET['client_error'] : false;
$delay = isset($_GET['delay']) ? $_GET['delay'] : 0;
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : false;

if ($serverError) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
}

if ($clientError) {
    header('HTTP/1.1 400 Bad Request', true, 400);
}

if ($delay > 0) {
    usleep($delay * 1000000);
}

if ($redirect) {
    header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']);
    echo 'Redirect';
} else {
    echo 'Ok';
}

fwrite(
    $file,
    json_encode(array(
        'SERVER' => $_SERVER,
        'GET'    => $_GET,
        'POST'   => $_POST,
        'FILES'  => $_FILES,
        'INPUT'  => file_get_contents('php://input'),
    ))
);

fflush($file);
flock($file, LOCK_UN);
fclose($file);
