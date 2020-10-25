<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 16:34.
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
require_once __DIR__.'/../../web/common/common.func.php';

class UpdateSiteCategory {
	public $description = '更新站点分类';

	public function up() {
		if (!pdo_fieldexists('site_category', 'multiid')) {
			pdo_query('ALTER TABLE '.tablename('site_category')." ADD `multiid` int(11) NOT NULL DEFAULT '0';");
		}
	}
}
