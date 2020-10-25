<?php
/**
* @package phpBB-WAP MODS
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

function validate_openid($openid)
{
	global $db;
	
	if ($openid != '')
	{
		$sql = 'SELECT qq_openid 
			FROM ' . USERS_TABLE . " 
			WHERE qq_openid = '" . $db->sql_escape($openid) . "'";
			
		if ($result = $db->sql_query($sql))
		{
			if ($db->sql_numrows($result))
			{
				return array('error' => true, 'error_msg' => '对不起，您已经绑定过论坛的其他帐号');
			}
		}
		
		return array('error' => false, 'error_msg' => '');

	}
	
	return array('error' => true, 'error_msg' => '对不起，Open ID为空，请稍后重试');
	
}

function gen_rand_string($hash)
{
	$chars = array( 'a', 'A', 'b', 'B', 'c', 'C', 'd', 'D', 'e', 'E', 'f', 'F', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'J',  'k', 'K', 'l', 'L', 'm', 'M', 'n', 'N', 'o', 'O', 'p', 'P', 'q', 'Q', 'r', 'R', 's', 'S', 't', 'T',  'u', 'U', 'v', 'V', 'w', 'W', 'x', 'X', 'y', 'Y', 'z', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
	
	$max_chars = count($chars) - 1;
	srand( (double) microtime()*1000000);
	
	$rand_str = '';
	for($i = 0; $i < 8; $i++)
	{
		$rand_str = ( $i == 0 ) ? $chars[rand(0, $max_chars)] : $rand_str . $chars[rand(0, $max_chars)];
	}

	return ( $hash ) ? md5($rand_str) : $rand_str;
}

function get_config()
{
	if (is_file(dirname(__FILE__) . '/conifg.php')) 
	{
		$config = require dirname(__FILE__) . '/conifg.php';
	}
	else
	{
		$config = array('appid' => '', 'appkey' => '', 'callback' => '');
	}
	
	return $config;
}

function sava_config($appid, $appkey, $callback)
{
	$config = array('appid' => $appid, 'appkey' => $appkey, 'callback' => $callback);
	$cachefile = dirname(__FILE__) . '/conifg.php';
	$fp = fopen($cachefile, 'w');
	$s = "<?php\r\n";
	$s .= 'return ' . var_export($config, true) . ";\r\n";
	$s .= '?>';
	fwrite($fp, $s);
	fclose($fp);
}

?>