<?php


/**
* Created by JetBrains PhpStorm.
* User: taoqili
* Date: 12-2-19
* Time: 下午10:44
* To change this template use File | Settings | File Templates.
*/

define('BBS_PATH', '../../../../');
$conf = include BBS_PATH.'conf/conf.php';
define('FRAMEWORK_PATH', BBS_PATH.'xiunophp/');
define('FRAMEWORK_TMP_PATH', $conf['tmp_path']);
define('FRAMEWORK_LOG_PATH', $conf['log_path']);
include FRAMEWORK_PATH.'core.php';
core::init($conf);
core::ob_start();

$srchkey = isset($_POST["searchKey"]) ? $_POST["searchKey"] : '';
$type = isset($_POST["videoType"]) ? $_POST["videoType"] : '';

$key = htmlspecialchars($srchkey);
$type = htmlspecialchars($type);

error_log('http://api.tudou.com/v3/gw?method=item.search&appKey=myKey&format=json&kw='.$key.'&pageNo=1&pageSize=20&channelId='.$type.'&inDays=7&media=v&sort=s', 3, 'd:/1.txt');

$html = misc::fetch_url('http://api.tudou.com/v3/gw?method=item.search&appKey=myKey&format=json&kw='.$key.'&pageNo=1&pageSize=20&channelId='.$type.'&inDays=7&media=v&sort=s');

// 获取头，如果为 Location, 则取 Location 的值。

echo $html;
exit;