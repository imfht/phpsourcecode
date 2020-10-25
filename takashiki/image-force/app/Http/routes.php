<?php

Route::post('/upload', 'MainController@upload');

Route::any('/{sha1}', 'MainController@view')->where(['sha1' => '\w+']);
