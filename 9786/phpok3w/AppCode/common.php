<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-3-20
 * Time: 上午11:11
 */
$L = array();
include DT_ROOT.'/lang/'.DT_LANG.'/lang.inc.php';

$CFG['timezone'] = 'Etc/GMT-8';
if(function_exists('date_default_timezone_set')) date_default_timezone_set($CFG['timezone']);
$DT_TIME = time() + $CFG['timediff'];