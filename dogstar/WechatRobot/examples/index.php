<?php
/**
 * 微信统一入口
 *
 * @author: dogstar 20150122
 */

/** ------ 如果是首次接入微信，请将下面注释临时去掉 ------**/
// echo $_GET['echostr'];
// die();

// 兼容获取参数
if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
    $GLOBALS['HTTP_RAW_POST_DATA'] = file_get_contents("php://input");
}

if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
    die('Access denied!');
}

require_once dirname(__FILE__) . '/MyRobot.php';

try {
    $robot = new MyRobot('YourTokenHere...', true);

    $rs = $robot->run();

    echo $rs;
} catch (Exception $ex) {
    //TODO: 出错的处理
}

