<?php
/**
 * User: dj
 * Date: 2020/9/11
 * Time: 上午1:23
 * Mail: github@djunny.com
 */

include '../src/spider.php';

use \ZV\Spider as spider;

$spider = new spider('http://127.0.0.1/post', [
]);

$spider->POST([
    'query' => 1,
    // upload
    'file1' => '@' . __FILE__,
    // upload file with MIME
    'file2' => '@' . __FILE__ . ';text/plain'
]);

print_r($spider->getBody());

