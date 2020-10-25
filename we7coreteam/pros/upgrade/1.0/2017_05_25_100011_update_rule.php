<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 17:10.
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
class UpdateRule {
	public function up() {
		$getall_containtype_data = pdo_getall('rule', array('containtype <>' => ''));
		foreach ($getall_containtype_data as $containtype_val) {
			$types = explode(',', $containtype_val['containtype']);
			if (in_array('image', $types)) {
				$new_containtype_val = str_replace('image', 'images', $containtype_val['containtype']);
				pdo_update('rule', array('containtype' => $new_containtype_val), array('id' => $containtype_val['id']));
			}
		}
	}
}
