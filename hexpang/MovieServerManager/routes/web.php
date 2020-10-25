<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
Route::get('/', 'ViewController@showView');
Route::get('/{view}/{action}', 'ViewController@showView');
Route::get('/{view}/{action}/{param}', 'ViewController@showView');
Route::get('/{view}/{action}/{param}/{param1}', 'ViewController@showView');
