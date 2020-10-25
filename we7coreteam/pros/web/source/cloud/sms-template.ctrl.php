<?php
/**
 * [WeEngine System] Copyright (c) 2014 W7.CC.
 */
defined('IN_IA') or exit('Access Denied');

$dos = array('display');
$do = in_array($do, $dos) ? $do : 'display';
template('cloud/sms-template');