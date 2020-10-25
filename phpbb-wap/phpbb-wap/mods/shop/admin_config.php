<?php

include 'shop.constants.php';

$sql = "SELECT config_name, config_value
	FROM " . SHOP_CONFIG_TABLE;

if(!$result = $db->sql_query($sql))
{
	trigger_error('无法取得虚拟商店的配置信息', E_USER_WARNING);
}
else
{
	while( $row = $db->sql_fetchrow($result) )
	{
		$config_name = $row['config_name'];
		$config_value = $row['config_value'];
		$default_config[$config_name] = isset($_POST['submit']) ? str_replace("'", "\'", $config_value) : $config_value;
		
		$new[$config_name] = ( isset($_POST[$config_name]) ) ? $_POST[$config_name] : $default_config[$config_name];

		if( isset($_POST['submit']) )
		{
			$sql = "UPDATE " . SHOP_CONFIG_TABLE . " SET
				config_value = '" . str_replace("\'", "''", $new[$config_name]) . "'
				WHERE config_name = '$config_name'";
			if( !$db->sql_query($sql) )
			{
				trigger_error("无法更新 shop_config 表 $config_name 的值", E_USER_WARNING);
			}
		}
	}

	if( isset($_POST['submit']) )
	{
		trigger_error('修改成功！' . back_link(append_sid('admin_mods.php?mode=admin&mods=shop&load=config')));
	}
}


$good_yes = ($new['good']) ? 'checked="checked"' : '';
$good_no = (!$new['good']) ? 'checked="checked"' : '';

$ad_yes = ($new['ad']) ? 'checked="checked"' : '';
$ad_no = (!$new['ad']) ? 'checked="checked"' : '';

$template->set_filenames(array(
	'body' => 'shop_admin_config.tpl')
);

$template->assign_vars(array(
	'U_ADMIN_MODS'		=> append_sid('admin_mods.php'),
	'GOOD_YES'			=> $good_yes,
	'GOOD_NO'			=> $good_no,
	'AD_YES'			=> $ad_yes,
	'AD_NO'				=> $ad_no,
	'TIME_CLICK'		=> $new['time_click'],
	'TOP_AD'			=> $new['top_ad'],
	'FOOT_AD'			=> $new['foot_ad'],
	'MAX_TOP_AD'		=> $new['max_top_ad'],
	'MAX_FOOT_AD'		=> $new['max_foot_ad'],
	'MIN_DAY'			=> $new['min_day'],
	'MAX_DAY'			=> $new['max_day'],
	'BUY_USERNAME'		=> $new['buy_username'],
	'BUY_RANK'			=> $new['buy_rank'],
	'BUY_NAMECOLOR'		=> $new['buy_namecolor'],
	'POINTS_NAME'		=> $board_config['points_name'],
	'U_BACK'			=> append_sid('admin_mods.php?mode=admin&mods=shop'),
	'S_ADMIN_CONFIG'	=> append_sid('admin_mods.php?mode=admin&mods=shop&load=config'))
);

$template->pparse('body');

?>