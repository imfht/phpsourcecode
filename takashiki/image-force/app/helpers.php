<?php

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

if (!function_exists('is_image')) {
    function is_image($mimeType)
    {
        return starts_with($mimeType, 'image/');
    }
}

if (!function_exists('is_available')) {
    function is_available($url, $timeout = 10)
    {
        try {
            return (new Client())->head($url, [
                'timeout' => $timeout,
                'connect_timeout' => $timeout,
            ])->getStatusCode() === 200;
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('are_available')) {
    function are_available($urls, $timeout = 10)
    {
        $client = new Client([
            'timeout' => $timeout,
            'connect_timeout' => $timeout,
        ]);
        $promises = array_map(function ($url) use ($client) {
            return $client->getAsync($url);
        }, $urls);

        try {
            $results = Promise\settle($promises)->wait();

            return array_map(function ($result) {
                return $result['value']->getStatusCode() === 200;
            }, $results);
        } catch (Exception $e) {
            return array_map(function ($url) {
                return false;
            }, $urls);
        }
    }
}
