<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('/', 'PublicController@login');
Route::get('public/login', 'PublicController@login');
Route::post('public/login', 'PublicController@doLogin');
Route::get('public/test', 'PublicController@test');
Route::get('public/logout', 'PublicController@logout');
Route::get('main/index', 'MainController@index');
Route::post('main/changPwd', 'MainController@changPwd');
Route::post('main/backToPrev', 'MainController@backToPrev');
Route::post('main/download', 'MainController@download');
Route::get('main/sortByFilename/{sort}', 'MainController@sortByFilename');
Route::get('main/sortBySize/{sort}', 'MainController@sortByFilename');
Route::get('main/sortByTime/{sort}', 'MainController@sortByFilename');
