<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 17:16.
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
class UpdateUniGroup {
	public function up() {
		load()->model('cache');
		$groups = pdo_getall('uni_group');
		$site_template = pdo_getall('site_templates', array(), array(), 'name');
		if (is_array($groups) && !empty($groups)) {
			foreach ($groups as $group) {
				$templates = iunserializer($group['templates']);
				$upgrade_template = array();
				if (is_array($templates) && !empty($templates)) {
					foreach ($templates as $key => $template) {
						if (!empty($template['name'])) {
							$upgrade_template[] = $site_template[$template['name']]['id'];
						} elseif (is_string($key)) {
							$upgrade_template[] = $site_template[$key]['id'];
						} else {
							$upgrade_template[] = $template;
						}
					}
				}
				$upgrade_template = iserializer(array_unique($upgrade_template));
				pdo_update('uni_group', array('templates' => $upgrade_template), array('id' => $group['id']));
			}
		}
		cache_build_uni_group();
	}
}
