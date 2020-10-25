<?php

Route::middleware('web')->get('/upload/bigfile', '\AuroraLZDF\Bigfile\Controllers\BigfileController@loadView')->name('bigfile_view');

// bindings:不限制API访问次数限制，不需要 csrf_token 验证
Route::middleware('bindings')->post('/upload/bigfile', '\AuroraLZDF\Bigfile\Controllers\BigfileController@upload')->name('bigfile_upload');