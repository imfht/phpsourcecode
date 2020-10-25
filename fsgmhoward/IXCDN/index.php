<?php
/**
 * This file is part of IXCDN
 * Published under MIT License
 * Copyright (c) Howard Liu, 2016
 * Please refer to LICENSE file for more information
 */

require 'vendor/autoload.php';

use App\Main;

if (isset($_GET['link'])) {
    $cdn = new Main(str_ireplace('link=', '', ($pos = stripos($_SERVER['QUERY_STRING'], '&'))
    ? substr_replace(
        $_SERVER['QUERY_STRING'],
        '?',
        $pos,
        1
    )
    : $_SERVER['QUERY_STRING']), 'cdn.ixnet.work', '*');
    echo $cdn->handler();
} else {
    header('HTTP/1.1 403 Access Denied');
    exit('403: Invalid Access');
}
