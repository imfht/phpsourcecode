<?php

$app = app();

$app->route('/', function () {
    return __FILE__;
}, null, null);

$app->route('/<string>/', function ($name) {
    return $name . ': ' . __FILE__;
});