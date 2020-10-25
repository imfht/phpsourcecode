<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 17:25.
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
class UpdateVfounder {
	public $description = 'ims_users表添加副创始人字段';

	public function up() {
		if (!pdo_fieldexists('users', 'founder_groupid')) {
			pdo_query('ALTER TABLE '.tablename('users')." ADD COLUMN `founder_groupid` TINYINT(4) NOT NULL DEFAULT 0  COMMENT '管理组，1是创始人，2是副创始人' AFTER `groupid`;");
		}
		if (!pdo_fieldexists('users', 'owner_uid')) {
			pdo_query('ALTER TABLE '.tablename('users')." ADD COLUMN `owner_uid` int(10) NOT NULL DEFAULT 0 COMMENT '副创始人uid' AFTER `uid`;");
		}
		if (!pdo_fieldexists('uni_group', 'owner_uid')) {
			pdo_query('ALTER TABLE '.tablename('uni_group')." ADD COLUMN `owner_uid` int(10) NOT NULL DEFAULT 0 COMMENT '副创始人uid' AFTER `id`;");
		}
		if (!pdo_fieldexists('users_group', 'owner_uid')) {
			pdo_query('ALTER TABLE '.tablename('users_group')." ADD COLUMN `owner_uid` int(10) NOT NULL DEFAULT 0 COMMENT '副创始人uid' AFTER `id`;");
		}
	}
}
