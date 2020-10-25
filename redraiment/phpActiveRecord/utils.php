<?php

function parseKeyParameter($name) {
    if (preg_match('/:$/', $name)) {
        $name = substr($name, 0, strlen($name) - 1);
    }
    return $name;
}

function toUnderscore($s) {
    return strtolower(preg_replace('/(?=[A-Z])/', '_', $s));
}

function toCamelCase($s) {
    return preg_replace_callback('/_([a-z])/', function($matches) {
        return strtoupper($matches[1]);
    }, $s);
}
