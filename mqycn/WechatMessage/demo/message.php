<?php
error_reporting(2047);

/**
 * 文件：message.php
 * 作者：mqycn
 * 博客：http://www.miaoqiyuan.cn
 * 源码：https://gitee.com/mqycn/WechatMessage
 * 说明：入口文件，需要定制回复内容请 修改 message/WechatMessageApp.php
 */
require_once 'message/WechatMessageApp.php';

$wechatObj = new WechatMessageApp();
echo $wechatObj->auto();
?>