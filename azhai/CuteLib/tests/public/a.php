<?php

$app = app();

$app->route('/', function () {
    return __FILE__;
});

$app->route('/b/', function () {
    return __FILE__;
});