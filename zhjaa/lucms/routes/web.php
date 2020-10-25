<?php

/*
Route::domain(env('APP_URL'))->group(function () {
});

Route::domain(env('MOBILE_APP_URL'))->namespace('Mobile')->group(function () {
    Route::get('/{lang?}', 'IndexController@index')->name('mobile.home');
});
*/

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('web.logs');
Route::get('/', 'IndexController@index')->name('web.home');
Route::get('/dashboard', 'IndexController@dashboard')->name('web.dashboard');
Route::get('/docs', 'IndexController@docs')->name('web.docs');

Route::get('/changeLocale/{locale}', 'IndexController@changeLocale')->name('web.change_locale');

