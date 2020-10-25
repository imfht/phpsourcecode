<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 16:42.
 */
namespace We7\V10;
defined('IN_IA') or exit('Access Denied');
class UpdateSiteArticle {
	public function up() {
		//新增微站文章管理->未排序时，文章按修改时间倒序排序
		if (!pdo_fieldexists('site_article', 'edittime')) {
			pdo_query('ALTER TABLE '.tablename('site_article')." ADD `edittime` INT(10) NOT NULL COMMENT '修改时间' AFTER `createtime`;");
		}
	}
}
