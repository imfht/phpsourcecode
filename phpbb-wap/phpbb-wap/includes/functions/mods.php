<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

/*
* 获取MOD的信息
*
* <代码>
* /*
* MOD名称: Hello World
* MOD支持地址: http://phpbb-wap.com
* MOD描述: Hello World插件
* MOD作者: 爱疯的云
* MOD版本: 1.0
* MOD显示: on或off
* * /
* </代码>
* 
* @参数 字符串 $mod_file 处理的标准mod文件
*/

function get_mod_data($mod_file)
{
	$mod_headers = array(
		'mod_name' 			=> 'MOD名称',
		'mod_support'		=> 'MOD支持地址',
		'mod_version' 		=> 'MOD版本',
		'mod_description' 	=> 'MOD描述',
		'mod_author' 		=> 'MOD作者',
		'mod_show'			=> 'MOD显示'
	);
	
	$fp = fopen($mod_file, 'r');
	
	$file_data = fread($fp, 8192);

	fclose($fp);
	
	$file_data = str_replace("\r", "\n", $file_data );
	
	foreach ($mod_headers as $field => $regex)
	{
		if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/' ) . ':(.*)$/mi', $file_data, $match) && $match[1])
		{
			$all_headers[$field] = $match[1];
		}
		else
		{
			$all_headers[$field] = '';
		}
	}
	
	return $all_headers;
}

/*
* 检查文件
* @参数 $filename 文件名
* 返回 
* 	值为真返回 true
*	否则返回 false
* 
* 说明：
*		管理层的文件以admin_xxx开头
*		不允许使用install.php和uninstall文件
*		对于引用的文件请先建立一个文件夹
*/
function mods_adminfile($filename)
{
	if (preg_match("/^admin_.*?\.php$/", $filename) || ($filename == 'admin.php'))
	{
		return true;
	}

	return false;
}

/*
* 验证MOD中的url地址
*/
function add_http($url)
{
	
	if ( preg_match('/http:\/\//', $url) || preg_match('/https:\/\//', $url) )
	{
		$url = $url;
	}
	else
	{
		$url = 'http://' . $url;
	}
	
	if (preg_match("#([\w]+?://[^[:space:]]*?)#is", $url))
	{
		$new_url = $url;
	}
	else
	{
		$new_url = '无';
	}
	
	return $new_url;
}

	
function get_mod_show($mod_show_value)
{
	if (trim($mod_show_value) == 'on')
	{
		return 1;
	}
	else
	{
		return 0;
	}
}

/*
* 执行MOD中的SQL，可以执行多条，用;分隔
*/
function run_query($sql_query)
{
	global $db, $table_prefix, $dbname;
	
	$sql_query = preg_replace('/phpbb_/', $table_prefix, $sql_query);
	
	$sql_query = remove_remarks($sql_query);
	
	$sql_query = split_sql_file($sql_query, ';');
	
	for ($i = 0; $i < count($sql_query); $i++)
	{
		if (trim($sql_query[$i]) != '')
		{
			if (!($db->sql_query($sql_query[$i])))
			{
				$error = $db->sql_error();
				echo $error['message'];
				echo '<br />';
			}
		}
	}
}
?>