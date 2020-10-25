<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 17:28
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
class CreateStatVisit {

	public $description = '添加访问统计表';

	public function up(){
		pdo_query("CREATE TABLE IF NOT EXISTS `ims_stat_visit` (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`module` varchar(100) NOT NULL,
`count` int(10) unsigned NOT NULL,
`date` date NOT NULL,
PRIMARY KEY (`id`),
KEY `date` (`date`) USING BTREE,
KEY `module` (`module`) USING BTREE,
KEY `uniacid` (`uniacid`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");

	}
}