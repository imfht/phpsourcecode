<?php
/**
 * route 使用参考：https://github.com/NauxLiu/route
 */

use fastwork\facades\Route;

Route::get('/test/:p?', 'Index/index');