#!/usr/bin/env php

<?php
/**
 * @author dogstar 20150408
 */

if ($argc < 3) {
    echo "Usage: $argv[0] <html_file> <save_folder> \n\n";
    exit(1);
}

$file = trim($argv[1]);
if (!file_exists($file)) {
    echo "Miss $file !\n\n";
    exit(2);
}


$folder = trim($argv[2]);
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

$info = pathinfo($file);

$header = file_get_contents(dirname(__FILE__) . '/header.html');
//$header = str_replace('<title>', '<title>' . $info['filename'] . ' | ', $header);
//$header = str_replace(',PHP接口框架">', ',PHP接口框架,phalapi文档,phalapi wiki,PhalApi文档,phalapi在线文档,phalapi官方文档">', $header);

$rs = file_get_contents($file);

$footer = file_get_contents(dirname(__FILE__) . '/footer.html');

$content = $header . "\n\n<!-- body start -->\n\n" . $rs . "\n\n<!-- body end -->\n\n" . $footer;

file_put_contents($folder . '/' . $info['filename'] . '.html', $content);

