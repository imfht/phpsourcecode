<?php
/**
 * [WeEngine System] Copyright (c) 2014 W7.CC.
 */
defined('IN_IA') or exit('Access Denied');
$account_api = WeAccount::createByUniacid();
if ('post-step' == $action) {
	define('FRAME', '');
} else {
	define('FRAME', $account_api->menuFrame);
}
