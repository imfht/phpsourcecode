<?php

/**
*	商店广告功能首页MBB扩展类
*/

class shop_module_bbcode
{
	var $_module;
	var $ad = array();

	function shop_module_bbcode(&$module)
	{
		$this->_module = $module;
	}

	function main()
	{
		global $table_prefix;

		$top = '';
		$foot = '';
		$sql = 'SELECT ad_id, ad_name, ad_type, ad_time, ad_url
			FROM ' . $table_prefix . 'shop_ad
			ORDER BY ad_id DESC';
		if ( !($result = $this->_module->_db->sql_query($sql)) ) trigger_error('无法查询广告信息', E_USER_WARNING);
		if ( $row = $this->_module->_db->sql_fetchrow($result) ) {
			$i = 0;
			do {
				if ( $row['ad_type'] ) {
					if ( $row['ad_time'] < time() ) $this->_module->_db->sql_query("DELETE FROM " . $table_prefix . "shop_ad WHERE ad_id = " . $row['ad_id']);
					else $top .= '<a href="' . $row['ad_url'] . '">' . $row['ad_name'] . '</a><br />';
				} else {
					if ( $row['ad_time'] < time() ) $this->_module->_db->sql_query("DELETE FROM " . $table_prefix . "shop_ad WHERE ad_id = " . $row['ad_id']);
					else $foot .= '<a href="' . $row['ad_url'] . '">' . $row['ad_name'] . '</a><br />';
				}
				$i++;
			}
			while ( $row = $this->_module->_db->sql_fetchrow($result) );
			$this->_module->_db->sql_freeresult($result);
		}

		$this->ad['top'] = $top;
		$this->ad['foot'] = $foot;

		$this->_module->reg_bbcode_exp('[显示顶部广告]', '加载商店中用户购买的顶部广告');
		$this->_module->reg_bbcode_exp('[显示底部广告]', '加载商店中用户购买的底部广告');
	}

	function bbcodes()
	{
		$this->_module->reg_bbcodes('str', '[显示顶部广告]', $this->ad['top']);
		$this->_module->reg_bbcodes('str', '[显示底部广告]', $this->ad['foot']);
	}

}