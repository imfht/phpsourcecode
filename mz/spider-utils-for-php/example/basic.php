<?php
/**
 * User: dj
 * Date: 2020/9/11
 * Time: 上午1:06
 * Mail: github@djunny.com
 */
include '../src/spider.php';

use \ZV\Spider as spider;

$spider = new spider('https://www.baidu.com/s?wd=爱情&pn=50&rn=50&tn=json', [
    //'User-Agent' => 'mobile',
    //'Cookie' => '',
    'proxy' => [
        // http proxy
        //'host' => '127.0.0.1:8899',
        //'type' => 'HTTP',//SOCKET
        //'auth' => ':user:pass',//NTLM:user:pass
    ]
]);

$spider->GET();
print_r($spider->getResponseCode());
print_r($spider->getResponseHeader());
print_r($spider->getBody());
print_r($spider->getUrl());
print_r($spider->getJson());
