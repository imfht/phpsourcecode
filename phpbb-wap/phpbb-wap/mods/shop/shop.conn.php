<?php

function verify_points($points) {
	
	global $userdata, $board_config;

	$result = array();

	if ($userdata['user_points'] >= $points)
	{
		$result['return'] = true;
		$result['message'] = '您现在有<strong>' . $userdata['user_points'] . $board_config['points_name'] . '</strong>，需要支付' . $points . $board_config['points_name'] .'来完成这个操作，确定下一步？';
	}
	else
	{
		$result['return'] = false;
		$result['message'] = '您现在有<strong>' . $userdata['user_points'] . $board_config['points_name'] . '</strong>，需要支付' . $points . $board_config['points_name'] .'来完成这个操作，非常抱歉，先去赚一点' . $board_config['points_name'] . '再来吧，我在等着你哦！';
	}

	return $result;
}

$service = array(
	'username' => '修改用户名',
	'rank' => '修改我的等级', 
	'namecolor' => '修改用户名颜色', 
	'qq' => '购买ＱＱ号码', 
	'ad' => '投放广告', 
	'good' => '精彩内容（可以获得' . $board_config['points_name'] . '哦）'
); 

include 'shop.constants.php';

$sql = "SELECT config_name, config_value
	FROM " . SHOP_CONFIG_TABLE;
if( !($result = $db->sql_query($sql)) )
{
	trigger_error('无法取得商店的配置信息', E_USER_WARNING);
}

while ( $row = $db->sql_fetchrow($result) )
{
	$shop_config[$row['config_name']] = $row['config_value'];
}

?>