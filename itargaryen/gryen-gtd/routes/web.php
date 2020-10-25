<?php

use Illuminate\Support\Facades\Route;

/**
 * 首页、关于页等.
 */
Route::get('/', 'HomeController@index');
Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/privacypolicy', 'HomeController@privacyPolicy');

Auth::routes();
Route::feeds();

/*
 * 文章
 */
Route::group(['prefix' => 'articles'], function () {
    Route::get('/', 'ArticlesController@index');
    Route::get('/tag/{tag}', 'ArticlesController@tag');
    Route::get('/show/{id}.html', 'ArticlesController@show');
});

/*
 * 需要用户权限的路由
 */
Route::group(['middleware' => 'auth'], function () {
    Route::get('/articles/create', 'ArticlesController@create');
    Route::get('/articles/edit/{id}', 'ArticlesController@edit');
    Route::post('/articles/store', 'ArticlesController@store');
    Route::post('/articles/update/{id}', 'ArticlesController@update');
    Route::post('/articles/updatestatus', 'ArticlesController@updateStatus');
    Route::post('/articles/cover/upload', 'ArticlesController@cover');
    Route::post('/files/upload', 'FilesController@upload');
});

Route::group(['middleware' => 'auth', 'prefix' => 'dashboard'], function () {
    Route::get('/', function () {
        return view('dashboard.home');
    });
});
