<?php

Route::group([
    'namespace'  => 'Gouguoyin\LogViewer\Controllers',
    'prefix'     => config('log-viewer.web_route'),
    'middleware' => config('log-viewer.web_middleware', 'web'),
], function () {
    Route::get('/', 'HomeController@home')->name('log-viewer-home');
    Route::get('download', 'HomeController@download')->name('log-viewer-download');
    Route::get('delete', 'HomeController@delete')->name('log-viewer-delete');
});
