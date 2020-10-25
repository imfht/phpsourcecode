<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 17:06.
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
class UpdateSettingTemplate {
	public function up() {
		//更改后台风格统一为官方默认风格
		global $_W;
		if ($_W['setting']['basic'] != 'default') {
			 setting_save(array('template' => 'default'), 'basic');
		}
	}
}
