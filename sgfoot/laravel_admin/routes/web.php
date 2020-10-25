<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::match(['get', 'post'], 'login')->uses('LoginController@login')->name('login');
Route::get('logout')->uses('LoginController@logout')->name('logout');
Route::group(['middleware' => 'check.login'], function () {
    Route::get('config')->uses('MainController@config')->name('config');
    Route::get('/')->uses('MainController@home')->name('home');
    Route::any('list')->uses('TableController@lists')->name('list');
    Route::post('del')->uses('TableController@del')->name('del');
    Route::any('form')->uses('TableController@form')->name('form');
    Route::any('xform')->uses('TableController@xform')->name('xform');
    Route::get('upload')->uses('UploadController@view')->name('upload');
    Route::post('upload_touch')->uses('UploadController@upload_touch')->name('upload_touch');
    Route::post('upload_base64')->uses('UploadController@upload_base64')->name('upload_base64');
    Route::get('excel')->uses('ExportController@view')->name('excel');
    Route::get('export')->uses('ExportController@export')->name('export');
    Route::post('import')->uses('ExportController@import')->name('import');
});
