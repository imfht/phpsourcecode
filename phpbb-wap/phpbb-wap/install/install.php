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

require_once 'install_install.php';

$install = isset($_GET['install']) ? $_GET['install'] : '';

// 全新安装
if ( $install == 'install' )
{
	
	define('IN_PHPBB', true);
	
	define('ROOT_PATH', './../');
	
	$userdata 	= array();
	$error 		= false;

	$confirm = (isset($_POST['confirm'])) ? true : false;
	$cancel = (isset($_POST['cancel'])) ? true : false;

	if (isset($_POST['install_step']) || isset($_GET['install_step']))
	{
		$install_step = (isset($_POST['install_step'])) ? $_POST['install_step'] : $_GET['install_step'];
	}
	else
	{
		$install_step = '';
	}

	$dbms = isset($_POST['dbms']) ? $_POST['dbms'] : '';
	$dbhost = (!empty($_POST['dbhost'])) ? $_POST['dbhost'] : 'localhost';
	
	$dbuser = (!empty($_POST['dbuser'])) ? $_POST['dbuser'] : '';
	$dbpasswd = (!empty($_POST['dbpasswd'])) ? $_POST['dbpasswd'] : '';
	$dbname = (!empty($_POST['dbname'])) ? $_POST['dbname'] : '';

	$table_prefix = (!empty($_POST['prefix'])) ? $_POST['prefix'] : '';

	$admin_name = (!empty($_POST['admin_name'])) ? $_POST['admin_name'] : '';
	$admin_pass1 = (!empty($_POST['admin_pass1'])) ? $_POST['admin_pass1'] : '';
	$admin_pass2 = (!empty($_POST['admin_pass2'])) ? $_POST['admin_pass2'] : '';

	$ftp_path = (!empty($_POST['ftp_path'])) ? $_POST['ftp_path'] : '';
	$ftp_user = (!empty($_POST['ftp_user'])) ? $_POST['ftp_user'] : '';
	$ftp_pass = (!empty($_POST['ftp_pass'])) ? $_POST['ftp_pass'] : '';

	$board_email = (!empty($_POST['board_email'])) ? $_POST['board_email'] : '';
	$script_path = (!empty($_POST['script_path'])) ? $_POST['script_path'] : str_replace('install', '', dirname($_SERVER['PHP_SELF']));

	if (!empty($_POST['server_name']))
	{
		$server_name = $_POST['server_name'];
	}
	else
	{
		if (!empty($_SERVER['SERVER_NAME']) || !empty($_ENV['SERVER_NAME']))
		{
			$server_name = (!empty($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : $_ENV['SERVER_NAME'];
		}
		else if (!empty($_SERVER['HTTP_HOST']) || !empty($_ENV['HTTP_HOST']))
		{
			$server_name = (!empty($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : $_ENV['HTTP_HOST'];
		}
		else
		{
			$server_name = '';
		}
	}

	if (!empty($_POST['server_port']))
	{
		$server_port = $_POST['server_port'];
	}
	else
	{
		if (!empty($_SERVER['SERVER_PORT']) || !empty($_ENV['SERVER_PORT']))
		{
			$server_port = (!empty($_SERVER['SERVER_PORT'])) ? $_SERVER['SERVER_PORT'] : $_ENV['SERVER_PORT'];
		}
		else
		{
			$server_port = '80';
		}
	}

	if (empty($admin_name) || $admin_pass1 != $admin_pass2 || empty($admin_pass1) || empty($dbhost) )
	{

		$instruction_text = '';

		if ($install_step == 'begin')
		{
			if (empty($dbhost))
			{
				$error .= '<p>数据库地址不能为空</p>';
			}

			if ($admin_pass1 != $admin_pass2 )
			{
				$error .= '<p>两次输入的密码不相等</p>';
			}

			if (empty($admin_pass1))
			{
				$error .= '<p>密码不能为空</p>';
			}

			if (empty($admin_name))
			{
				$error .= '<p>用户名不能为空</p>';
			}
		}
		
		$s_hidden_fields = '<input type="hidden" name="install_step" value="begin" />';
		phpbb_install_header('', 'install.php?install=install');

		// 提示
		if ($error)
		{
			echo <<<HTML
			<div class="error">
				<span class="red">
					{$error}
				</span>
			</div>
HTML;
		}

		phpbb_install_title('Mysql');
		
		$dbhost_right_value = ($dbhost != '') ? $dbhost : '';
		$dbhost_right = '<input type="text" name="dbhost" value="' . $dbhost_right_value . '"/>';
		phpbb_install_tbody('数据库地址', $dbhost_right);
		
		$dbname_right_value = ($dbname != '') ? $dbname : '';
		$dbname_right = '<input type="text" name="dbname" value="' . $dbname_right_value . '" />';
		phpbb_install_tbody('数据库名', $dbname_right);
		
		$dbuser_right_value = ($dbuser != '') ? $dbuser : '';
		$dbuser_right = '<input type="text" name="dbuser" value="' . $dbuser_right_value . '" />';
		phpbb_install_tbody('数据库用户名', $dbuser_right);

		$dbpasswd_right_value = ($dbpasswd != '') ? $dbpasswd : '';
		$dbpasswd_right = '<input type="password" name="dbpasswd" value="' . $dbpasswd_right_value . '" />';
		phpbb_install_tbody('数据库密码', $dbpasswd_right);

		$prefix_right_value = (!empty($table_prefix)) ? $table_prefix : "phpbb_";
		$prefix_right = '<input type="text" name="prefix" value="' . $prefix_right_value . '" />';
		phpbb_install_tbody('表前缀', $prefix_right);
		
		phpbb_install_title('超级管理员');

		$admin_name_right_value = ($admin_name != '') ? $admin_name : '';
		$admin_name_right = '<input type="text" name="admin_name" value="' . $admin_name_right_value . '" />';
		phpbb_install_tbody('用户名', $admin_name_right);

		$admin_pass1_right_value = ($admin_pass1 != '') ? $admin_pass1 : '';
		$admin_pass1_right = '<input type="password" name="admin_pass1" value="' . $admin_pass1_right_value . '" />';
		phpbb_install_tbody('密码', $admin_pass1_right);

		$admin_pass2_right_value = ($admin_pass2 != '') ? $admin_pass2 : '';
		$admin_pass2_right = '<input type="password" name="admin_pass2" value="' . $admin_pass2_right_value . '" />';
		phpbb_install_tbody('确认密码', $admin_pass2_right);
		
		phpbb_install_common_form($s_hidden_fields, '开始安装');
		
		phpbb_install_footer();
		exit;
	}
	else
	{
		require(ROOT_PATH . 'includes/constants.php');
		require(ROOT_PATH . 'includes/functions/common.php');
		require(ROOT_PATH . 'includes/class/session.php');

		require(ROOT_PATH . 'includes/class/mysql.php');

		$db = new sql_db($dbhost, $dbuser, $dbpasswd, $dbname, false);

		if(!$db->db_connect_id)
		{
			die('<!DOCTYPE HTML><html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /><title>提示</title><style type="text/css">@charset "utf-8";*{margin:0;padding:0;}body{margin:0 auto;max-width:640px;font-family:"Century Gothic","Microsoft yahei";background-color:#F9F9F9;}#wrap{background-color:#FFF;width:640px;}.error{padding:20px;margin:0;border-style:solid;border-width:1px;border-color:#000;}.main{padding:115px 0 6px 0;}</style></head><body><div id="wrap"><div class="main"><div class="error"><p style="color:red;">无法链接到数据库，请检查您的数据库配置文件是否正确</p></div></div><div></body></html>');
		}

		$db->sql_query('SET NAMES utf8');

		$dbms_schema = 'schemas/mysql_schema.sql';
		$dbms_basic = 'schemas/mysql_basic.sql';
		$remove_remarks 	= 'remove_remarks';
		$delimiter 			= ';'; 
		$delimiter_basic 	= ';'; 

		include(ROOT_PATH.'includes/functions/sql_parse.php');

		$sql_query = @fread(@fopen($dbms_schema, 'r'), @filesize($dbms_schema));
		$sql_query = preg_replace('/phpbb_/', $table_prefix, $sql_query);

		$sql_query = $remove_remarks($sql_query);
		$sql_query = split_sql_file($sql_query, $delimiter);

		$db->sql_query("ALTER DATABASE " . $dbname . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");

		$sql_error = false;

		$sql_list = '';
		for ($i = 0; $i < count($sql_query); $i++)
		{
			if (trim($sql_query[$i]) != '')
			{
				

				if (!$db->sql_query($sql_query[$i]))
				{
					$db_error = $db->sql_error();
					$sql_list .= '<p>' . $sql_query[$i] . '</p>';
					$sql_list .= '<p>' . $db_error['message'] . '<p>';
					$sql_list .= '<hr />';
					$sql_error = true;
				}
			}
		}

		if ($sql_error)
		{
			phpbb_install_error_header('安装出错');
			phpbb_install_error('安装过程出错', '<p>很遗憾，没有安装成功，因为有一些SQL语句没有成功执行：</p><hr />' . $sql_list . '<p>您可以将上面的SQL使用电子邮件发送到 bug@phpbb-wap.com 进行报告</p>');
			phpbb_install_footer();
			exit;
		}

		$sql_query = @fread(@fopen($dbms_basic, 'r'), @filesize($dbms_basic));
		$sql_query = preg_replace('/phpbb_/', $table_prefix, $sql_query);

		$sql_query = $remove_remarks($sql_query);
		$sql_query = split_sql_file($sql_query, $delimiter_basic);

		$sql_list = '';

		for($i = 0; $i < count($sql_query); $i++)
		{
			if (trim($sql_query[$i]) != '')
			{
				if (!$db->sql_query($sql_query[$i]))
				{
					$db_error = $db->sql_error();
					$sql_list .= '<p>' . $sql_query[$i] . '</p>';
					$sql_list .= '<p>' . $db_error['message'] . '<p>';
					$sql_list .= '<hr />';
					$sql_error = true;
				}
			}
		}


		if ($sql_error)
		{
			phpbb_install_error_header('安装出错');
			phpbb_install_error('安装过程出错', '<p>很遗憾，没有安装成功，因为有一些SQL语句没有成功执行：</p><hr />' . $sql_list . '<p>您可以将上面的SQL使用电子邮件发送到 bug@phpbb-wap.com 进行报告</p>');
			phpbb_install_footer();
			exit;
		}

		$error = '';

		$sql = "INSERT INTO " . $table_prefix . "config (config_name, config_value) 
			VALUES ('board_startdate', " . time() . ")";
		if (!$db->sql_query($sql))
		{
			$error .= "<p>Could not insert board_startdate :: " . $sql . " :: " . __LINE__ . " :: " . __FILE__ . "</p>";
		}

		
		$update_config = array(
			'script_path'	=> $script_path,
			'server_port'	=> $server_port,
			'server_name'	=> $server_name,
		);

		foreach($update_config as $config_name => $config_value)
		{
			$sql = "UPDATE " . $table_prefix . "config 
				SET config_value = '$config_value' 
				WHERE config_name = '$config_name'";
			if (!$db->sql_query($sql))
			{
				$error .= "Could not insert default_lang :: " . $sql . " :: " . __LINE__ . " :: " . __FILE__ . "<br /><br />";
			}
		}

		$admin_pass_md5 = ($confirm && $userdata['user_level'] == ADMIN) ? $admin_pass1 : md5($admin_pass1);

		$sql = "UPDATE " . $table_prefix . "users 
			SET username = '" . str_replace("\'", "''", $admin_name) . "', user_password='" . str_replace("\'", "''", $admin_pass_md5) . "', user_email='" . str_replace("\'", "''", $board_email) . "'
			WHERE username = 'admin'";
		if (!$db->sql_query($sql))
		{
			$error .= "Could not update admin info :: " . $sql . " :: " . __LINE__ . " :: " . __FILE__ . "<br /><br />";
		}

		$sql = "UPDATE " . $table_prefix . "users 
			SET user_regdate = " . time();
		if (!$db->sql_query($sql))
		{
			$error .= "Could not update user_regdate :: " . $sql . " :: " . __LINE__ . " :: " . __FILE__ . "<br /><br />";
		}

		if ($error != '')
		{
			phpbb_install_error_header('安装出错');
			phpbb_install_error('安装过程出错', '<p>很遗憾，没有安装成功，因为有一些SQL语句没有成功执行：</p>' . $error . '<p>您可以将上面的错误信息使用电子邮件发送到 bug@phpbb-wap.com 进行报告</p>');
			phpbb_install_footer();
			exit;
		}

		// 写入config.php
		$config_data = '<?php'."\n\n";
		$config_data .= '$dbhost = \'' . $dbhost . '\';' . "\n";
		$config_data .= '$dbname = \'' . $dbname . '\';' . "\n";
		$config_data .= '$dbuser = \'' . $dbuser . '\';' . "\n";
		$config_data .= '$dbpasswd = \'' . $dbpasswd . '\';' . "\n";
		$config_data .= '$table_prefix = \'' . $table_prefix . '\';' . "\n";
		$config_data .= 'define(\'PHPBB_INSTALLED\', true);'."\n\n";	
		$config_data .= '?' . '>';

		@umask(0111);
		$no_open = FALSE;

		$fp = @fopen(ROOT_PATH . 'config.php', 'w');
		$result = @fputs($fp, $config_data, strlen($config_data));
		@fclose($fp);

		$s_hidden_fields = '<input type="hidden" name="username" value="' . $admin_name . '" />';
		$s_hidden_fields .= '<input type="hidden" name="password" value="' . $admin_pass1 . '" />';
		$s_hidden_fields .= '<input type="hidden" name="redirect" value="" />';
		$s_hidden_fields .= '<input type="hidden" name="autologin" value="0" />';
		$s_hidden_fields .= '<input type="hidden" name="login" value="true" />';		
		
		$page_body = '<div class="title">恭喜，安装完成！</div>';

		phpbb_install_header($page_body, '../login.php');
		phpbb_install_common_form($s_hidden_fields, '登录网站');
		phpbb_install_footer();
		exit;
	}


}
else if($install == 'update')
{
	define('IN_PHPBB', true);
	define('ROOT_PATH', './../');

	@include(ROOT_PATH . 'config.php');

	if ( isset($_POST['updating']) )
	{

		require(ROOT_PATH . 'includes/constants.php');
		require(ROOT_PATH . 'includes/functions/common.php');
		require(ROOT_PATH . 'includes/class/session.php');

		require(ROOT_PATH . 'includes/class/mysql.php');

		$db = new sql_db($dbhost, $dbuser, $dbpasswd, $dbname, false);

		if(!$db->db_connect_id)
		{
			die('<!DOCTYPE HTML><html><head><meta http-equiv="content-type" content="text/html; charset=utf-8" /><title>提示</title><style type="text/css">@charset "utf-8";*{margin:0;padding:0;}body{margin:0 auto;max-width:640px;font-family:"Century Gothic","Microsoft yahei";background-color:#F9F9F9;}#wrap{background-color:#FFF;width:640px;}.error{padding:20px;margin:0;border-style:solid;border-width:1px;border-color:#000;}.main{padding:115px 0 6px 0;}</style></head><body><div id="wrap"><div class="main"><div class="error"><p style="color:red;">无法链接到数据库，请检查您的数据库配置文件是否正确</p></div></div><div></body></html>');
		}

		include(ROOT_PATH.'includes/functions/sql_parse.php');
		
		$db->sql_query('SET NAMES utf8');

		$update_sql = 'schemas/mysql_update.sql';

		$sql_query = @fread(@fopen($update_sql, 'r'), @filesize($update_sql));

		$sql_query = preg_replace('/phpbb_/', $table_prefix, $sql_query);

		$sql_query = remove_remarks($sql_query);
		$sql_query = split_sql_file($sql_query, ';');

		$db->sql_query("ALTER DATABASE " . $dbname . " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");

		$update_error = false;
		$sql_list = '';
		for ($i = 0; $i < count($sql_query); $i++)
		{
		
			if (trim($sql_query[$i]) != '')
			{
				if (!$db->sql_query($sql_query[$i]))
				{
					$db_error = $db->sql_error();
					$sql_list .= '<p>' . $sql_query[$i] . '</p>';
					$sql_list .= '<p>' . $db_error['message'] . '<p>';
					$sql_list .= '<hr />';
					$update_error = true;
				}
			}
		}

		phpbb_update_complete($update_error, $sql_list);
	}
	else
	{
		phpbb_update_wellcome();
	}
}
else if($install == 'check')
{
	define('IN_PHPBB', true);
	define('ROOT_PATH', './../');

	$table_prefix = '';

	require(ROOT_PATH . 'includes/constants.php');
	require(ROOT_PATH . 'includes/functions/common.php');

	phpbb_install_check();
}
else
{
	phpbb_install_wellcome();
}
?>