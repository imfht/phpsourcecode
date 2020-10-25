<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 17:07.
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
class UpdateWxappVersion {
	public function up() {
		//更新小程序相关表结构
		if (!pdo_fieldexists('wxapp_versions', 'template')) {
			pdo_query('ALTER TABLE '.tablename('wxapp_versions')." CHANGE `template` `template` INT(10) NULL COMMENT '模板风格ID';");
		}
		if (!pdo_fieldexists('wxapp_versions', 'multiid')) {
			pdo_query('ALTER TABLE '.tablename('wxapp_versions')." CHANGE `multiid` `multiid` INT(10) UNSIGNED NOT NULL COMMENT '对应微站ID';");
		}
		if (!pdo_fieldexists('wxapp_versions', 'quickmenu')) {
			pdo_query('ALTER TABLE '.tablename('wxapp_versions')." CHANGE `quickmenu` `quickmenu` VARCHAR(2500) NULL COMMENT '快捷菜单';");
		}
		if (!pdo_fieldexists('wxapp_versions', 'version')) {
			pdo_query('ALTER TABLE '.tablename('wxapp_versions')." CHANGE `version` `version` VARCHAR(20) NULL COMMENT '版本号';");
		}
		if (!pdo_fieldexists('wxapp_versions', 'design_method')) {
			pdo_query('ALTER TABLE '.tablename('wxapp_versions')." CHANGE `design_method` `design_method` TINYINT(1) NULL COMMENT '1为DIY，2为模板，3为直接跳转';");
		}
	}
}
