<?php

// 检测环境是否支持可写
define('IS_WRITE',APP_MODE !== 'sae');

/**
 * 写入配置文件
 * @param  array $config 配置信息
 */
function write_config($config, $auth){
	if(is_array($config)){
		//读取配置内容
		$conf = file_get_contents(MODULE_PATH . 'sqldata/conf.tpl');
		$user = file_get_contents(MODULE_PATH . 'sqldata/user.tpl');
		//替换配置项
		foreach ($config as $name => $value) {
			$conf = str_replace("[{$name}]", $value, $conf);
			$user = str_replace("[{$name}]", $value, $user);
		}

		//写入应用配置文件
        file_put_contents('./App/Common/Conf/config.php', $conf);
        file_put_contents('./App/User/Conf/config.php', $user);
		return '';
		

	}
}



/**
 * 生成系统AUTH_KEY
 */
function build_auth_key(){
	$chars  = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$chars .= '`~!@#$%^&*()_+-=[]{};:"|,.<>/?';
	$chars  = str_shuffle($chars);
	return substr($chars, 0, 40);
}

function register_administrator($db, $prefix, $admin, $auth){
	
	$sql = "INSERT INTO `[PREFIX]ucenter_member` VALUES " .
		   "('1', '[NAME]', '[PASS]', '[EMAIL]', '', '[TIME]', '[IP]', 0, 0, '[TIME]', '1')";

	$password = user_md5($admin['admin_pass'], $auth);
	$sql = str_replace(
		array('[PREFIX]', '[NAME]', '[PASS]', '[EMAIL]', '[TIME]', '[IP]'),
		array($prefix, $admin['admin_user'], $password, $admin['admin_email'], NOW_TIME, get_client_ip(1)),
		$sql);
	//执行sql
	$db->execute($sql);

	$sql = "INSERT INTO `[PREFIX]member` VALUES ".
		   "('1', '[NAME]', '0', '0000-00-00', '', '0', '1', '0', '[TIME]', '0', '[TIME]', '1','',0,0,0,0,0);";
	$sql = str_replace(
		array('[PREFIX]', '[NAME]', '[TIME]'),
		array($prefix, $admin['admin_user'], NOW_TIME),
		$sql);
	$db->execute($sql);
	return true;
	
}
/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function user_md5($str, $key = ''){
	return '' === $str ? '' : md5(sha1($str) . $key);
}
