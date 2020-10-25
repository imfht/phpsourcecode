<?php

/**
 * 关闭订单
 * 执行时间：每隔1小时执行一次
 * 
 * @author chenjiebin <sjlinyu@qq.com>
 */
define("APPLICATION_PATH", realpath(dirname(__FILE__) . '/../../../')); //指向public的上一级
require APPLICATION_PATH . '/scripts/crontab/common.php';

echo "close expired order";
