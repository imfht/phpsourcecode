<?php
include_once 'conf.php';
include_once '../library/inc.php';
include LIB_PATH . 'cls.api.php';

// 判断来路并实例化相关类
if (empty($m)) {
  die($GLOBALS['lang']['404']);
}

if (!class_exists($m)) {
  die($GLOBALS['lang']['illegal']);
}

$api = new $m();

if (method_exists($api, $c)) {
  $api->{$c}($_GET);
}

unset($api);