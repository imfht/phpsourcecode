<?php
/**
 * 通过管道更新IP
 *
 * @example /sbin/ifconfig -a|grep inet|grep -v 127.0.0.1|grep -v inet6|awk '{print $2}'|tr -d "addr:" | php load.php
 * @package Controller
 * @author chengxuan <chengxuan@staff.weibo.com>
 */
include 'common.php';
Comm::ddns();
