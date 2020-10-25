<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 17:14.
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
class UpdateUniaccount {
	public $description = '更新UniAccount首字母';
	public function up() {
		if (pdo_fieldexists('uni_account', 'title_initial')) {
			$accounts = pdo_getall('uni_account', array(), array('name', 'uniacid', 'default_acid', 'title_initial'));
			if (!empty($accounts)) {
				foreach ($accounts as $account) {
					if (empty($account['title_initial'])) {
						try{
							$first_char = get_first_pinyin($account['name']);
							pdo_update('uni_account', array('title_initial' => $first_char), array('uniacid' => $account['uniacid'], 'default_acid' => $account['default_acid']));
						}catch (\Throwable $e) {
							echo $e;
						}
					}
				}
			}
		}
	}
}
