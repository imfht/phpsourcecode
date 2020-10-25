<?php
defined('BASE_PATH') OR exit('No direct script access allowed');
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 */

$config['route'] = [
    '/' => [
        'request_method' => ['GET','POST'],
        'controller' => 'App\\Cgi\\Controller\\UserController',
        'method' => 'index'
    ],
    '/user' => [
        'request_method' => ['GET'],
        'controller' => 'App\\Cgi\\Controller\\UserController',
        'method' => 'getUser'
    ],
];
