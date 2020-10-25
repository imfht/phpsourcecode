<?php
header('Content-Type: text/html; charset=utf-8');
define('NL', "<br><Br>\r\n");

include 'pinyin.class.php';

$str = '做法是默认的编码方式。对于英-文-文-件是ASCII编码，对于简体中文文件是GB2312编码..（只针对Windows简体中文版，如果是繁体中文版会采用Big5码）魍魉,交媾,蒯草';
echo "原文：", NL;
echo $str, NL;

$time = microtime(1);
echo "默认模式:", NL;
echo pinyin::get($str, 0, '', '-'), NL;
echo '<!--' . (microtime(1) - $time) . '-->', NL;

$time = microtime(1);
echo "全拼音+带分隔线:", NL;
echo pinyin::get($str, 0, '-'), NL;
echo '<!--' . (microtime(1) - $time) . '-->', NL;

$time = microtime(1);
echo "拼音首字母+带分隔线:", NL;
echo pinyin::get($str, 1, '-'), NL;
echo '<!--' . (microtime(1) - $time) . '-->', NL;

$time = microtime(1);
echo "拼音首字母+保留某些字符（,和.）:", NL;
echo pinyin::get($str, 1, '-', ',\.'), NL;
echo '<!--' . (microtime(1) - $time) . '-->', NL;
?>