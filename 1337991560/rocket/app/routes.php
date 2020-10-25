<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * 路由
 * @author 徐亚坤 hdyakun@sina.com
 */

Madphp\Route::get('/', 'admin/index@index');
Madphp\Route::get('/test', 'admin/index@test');
Madphp\Route::get('/base', 'base@index');

Madphp\Route::error(function () {
    throw new \Exception("404 Not Found");
});

Madphp\Route::dispatch();