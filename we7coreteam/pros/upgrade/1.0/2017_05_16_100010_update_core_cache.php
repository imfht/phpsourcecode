<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 17:08.
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
class UpdateCoreCache {
	public function up() {
		//发送消息缓存key的长度问题（原为200）  修改为100
		if (pdo_fieldexists('core_cache', 'key')) {
			pdo_query('ALTER TABLE '.tablename('core_cache')." MODIFY  `key` VARCHAR(100) NOT NULL COMMENT '缓存键名';");
		}
	}
}
