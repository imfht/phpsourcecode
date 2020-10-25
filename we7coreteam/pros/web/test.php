<?php
/**
 * [WeEngine System] Copyright (c) 2014 W7.CC
 * $sn: pro/web/index.php : v 14b9a4299104 : 2015/09/11 10:44:21 : yanghf $.
 */
define('IN_SYS', true);
require '../framework/bootstrap.inc.php';
require IA_ROOT . '/web/common/bootstrap.sys.inc.php';
load()->web('common');
load()->web('template');
load()->func('communication');
load()->model('cache');
load()->model('cloud');
load()->classs('coupon');
load()->func('file');
load()->func('db');
load()->web('tpl');
load()->classs('cloudapi');
var_dump(urlencode(' '));
$html = '<script'.urldecode('%0a').'language="php">phpinfo();</script>';
$html = preg_replace('/<\s*?script.*[\n\f\r\t\v]*.*(src|language)+/i', '_', $html);
var_dump($html);
echo $html;

