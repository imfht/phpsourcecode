<?php


use think\facade\Route;


Route::get('news', 'index/api_v1.news/index');

Route::post('login', 'index/api_v1.auth/login');



