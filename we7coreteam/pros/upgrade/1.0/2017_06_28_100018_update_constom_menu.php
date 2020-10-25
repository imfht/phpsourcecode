<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 17:24.
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
class UpdateConstomMenu {
	public $description = '优化自定义菜单后，删除数据库中默认菜单冗余数据';

	public function up() {
		$all_currentselfmenu = pdo_getall('uni_account_menus', array('type' => 1));
		foreach ($all_currentselfmenu as &$menu) {
			$menu['data'] = iunserializer(base64_decode($menu['data']));
			if (isset($menu['data']['matchrule'])) {
				unset($menu['data']['matchrule']);
			}
			if (isset($menu['data']['type'])) {
				unset($menu['data']['type']);
			}
			if (empty($menu['data']) || empty($menu['data']['button'])) {
				pdo_delete('uni_account_menus', array('id' => $menu['id']));
			} else {
				$newmenudata = base64_encode(iserializer($menu['data']));
				pdo_update('uni_account_menus', array('data' => $newmenudata), array('id' => $menu['id']));
			}
		}
	}
}
