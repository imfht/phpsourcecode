<?php

app()->route('/<string>/', function ($name) {
    return $name . ': ' . __FILE__;
});