<?php

if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
}

$sql = "SELECT *
		FROM ". ALBUM_CONFIG_TABLE;

if(!$result = $db->sql_query($sql))
{
	trigger_error('无法取得相册的配置信息', E_USER_WARNING);
}

while( $row = $db->sql_fetchrow($result) )
{
	$album_config_name = $row['config_name'];
	$album_config_value = $row['config_value'];
	$album_config[$album_config_name] = $album_config_value;
}

$template->assign_vars(array(
	'ALBUM_VERSION' => '2' . $album_config['album_version']
	)
);

include(ROOT_PATH . 'includes/album/functions.php');

?>