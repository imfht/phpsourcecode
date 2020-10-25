<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 17:31
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
class CreateVfounderGroup {
	
	public $description = "增加副创始人组表";

	public function up() {

		pdo_query("CREATE TABLE IF NOT EXISTS `ims_users_founder_group` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`name` varchar(50) NOT NULL,
`package` varchar(5000) NOT NULL DEFAULT '',
`maxaccount` int(10) unsigned NOT NULL DEFAULT '0',
`maxsubaccount` int(10) unsigned NOT NULL COMMENT '子公号最多添加数量，为0为不可以添加',
`timelimit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户组有效期限',
`maxwxapp` int(10) unsigned NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");

	}
}