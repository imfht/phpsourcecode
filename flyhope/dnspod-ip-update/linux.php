<?php
/**
 * Linux执行方法
 *
 * @package Controller
 * @author chengxuan <i@chengxuan.li>
 */

include 'common.php';


$ip = `/sbin/ifconfig -a|grep inet|grep -v 127.0.0.1|grep -v inet6|awk '{print $2}'|tr -d "addr:"`;
Comm::updateRecord($ip);
