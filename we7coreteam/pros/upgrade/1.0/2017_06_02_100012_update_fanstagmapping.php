<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 17:12.
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
class UpdateFanstagmapping {
	public function up() {
		if (pdo_fieldexists('mc_fans_tag_mapping', 'tagid')) {
			pdo_query('ALTER TABLE '.tablename('mc_fans_tag_mapping')." CHANGE `tagid` `tagid` INT(11) UNSIGNED NOT NULL COMMENT '公众号用户标签ID';");
		}
	}
}
