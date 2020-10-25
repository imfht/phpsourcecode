<?php
/**
 * Sign PNG
 *
 * @author PMonkey_W
 * @copyright Changsha Sinlody Network & Technology Co. Ltd.
 * @link www.sinlody.com
 * @since 2016-01-11
 * @version 1.0
 */

define('IN_API', true);
define('CURSCRIPT', 'api');
define('DISABLEXSSCHECK', true);

require '../../source/class/class_core.php';

$discuz = C::app();
$discuz->init();

require_once DISCUZ_ROOT.'./api/qrcode/qrcode.php';
$url = urldecode($_GET['url']);
ob_end_clean();
QRcode::png($url);