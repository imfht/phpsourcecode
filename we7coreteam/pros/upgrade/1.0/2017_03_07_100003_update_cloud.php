<?php
/*
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 16:42
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
class UpdateCloud {

	public $description = '微擎1.0内测用户云参数错误，导致提示升级模块到最新版本的bug';

	public function up() {

		load()->model('cache');
		load()->classs('cloudapi');
		$cloud_api = new \CloudApi();
//获取本地模块
		$module_lists = pdo_getall('modules', array('issystem !=' => '1'), array('version', 'name'), 'name');
		$we7_coupon = pdo_get('modules', array('name' => 'we7_coupon'), array('version', 'name'), 'name');
		$module_lists[$we7_coupon['name']] = $we7_coupon;
		if (!empty($module_lists)) {
			foreach ($module_lists as $key => $value) {
				if (!empty($value) && !empty($key)) {
					$lists[$key] = $value['version'];
				}
			}
		}
//调用云api获取模块setting信息
		$module_settings = $cloud_api->post('site', 'module_setting', array('modules' => $lists), 'json');
		if (!empty($module_settings)) {
			foreach ($module_settings as $k => $module) {
				if ($module['setting'] == 2 && !empty($module['name'])) {
					$setting_lists[$module['name']] = $module;
				}
			}
		}
		if (!empty($setting_lists)) {
			foreach ($setting_lists as $name => $setting) {
				pdo_update('modules', array('settings' => 2), array('name' => $name));
			}
		}
		cache_build_account_modules();
	}
}