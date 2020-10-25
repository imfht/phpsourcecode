<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/6
 * Time: 16:28.
 */
header('Location: ' . url('account/display'));
exit();
defined('IN_IA') or exit('Access Denied');
define('FRAME', 'advertisement');
if ('display' == $do) {
	define('ACTIVE_FRAME_URL', url('advertisement/content-provider/account_list'));
}
