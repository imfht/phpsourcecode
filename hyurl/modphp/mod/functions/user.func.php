<?php
/**
 * _user() 存储或者获取用户元信息
 * @param  string $key   信息名称
 * @param  [type] $value 信息值
 * @return mixed         如未设置 $key，则返回所有存储的信息；
 *                       如只设置 $key，则返回对应的信息，不存在则返回 null；
 *                       如果同时设置 $key 和 $value，则将 $key 对应的值设置为 $value 并始终返回 $value。
 */
function _user($key = "", $value = null){
	static $user = array();
	if(!$key) return $user ?: null;
	if($value === null){
		return isset($user[$key]) ? $user[$key] : null;
	}else{
		return $user[$key] = $value;
	}
}

/**
 * get_me() 当前登录用户信息获取函数
 * @param  string $key  [可选]数组键名
 * @return array|string 如果未设置 $key 参数，则返回整个数组，
 *                      否则返回数组键名为 $key 所对应的值，结果为空则返回 null
 */
function get_me($key = ''){
	static $result = array();
	static $sid = ''; //会话 ID，运行在 Socket 服务器模式时，ModPHP 会根据会话 ID 来区别每一个客户端
	if(!$result || $sid != session_id()){
		$sid = session_id();
		$result = array();
		$_result = user::getMe();
		error(null); //清空错误消息（如果有）
		if(!$_result['success']) return null;
		else $result = $_result['data'];
	}
	if(!$key) return $result ?: null;
	else if(isset($result[$key])) return $result[$key];
	else if(strpos($key, 'user_') !== 0){
		$key = 'user_'.$key;
		return isset($result[$key]) ? $result[$key] : null;
	}
}

/** is_logined() 判断用户是否登录 */
function is_logined(){
	return get_me() != null;
}
function_alias('is_logined', 'is_logged_in');

/** is_admin() 判断当前用户是否为管理员 */
function is_admin(){
	return me_level() == config('user.level.admin');
}

/** is_editor() 判断当前用户是否为编辑 */
function is_editor(){
	return me_level() == config('user.level.editor');
}

/**
 * is_profile() 判断当前页面是否为用户详情页
 * @param  mixed   $key [可选]如果为整数，则判断是否为 ID 是否是 $key 的用户详情页
 *                      如果为字符串，则判断是否是用户名为 $key 的用户详情页
 *                      如果为数组，则按数组内容逐一判断
 *                      如果不设置，则仅判断是否为用户详情页
 * @return boolean      成功返回 true, 失败返回 false
 */
function is_profile($key = 0){
	if(is_template(config('user.template'))){
		if($key && is_numeric($key)){
			return user_id() == $key;
		}elseif($key && is_string($key)){
			return user_name() == $key;
		}elseif(is_array($key)){
			foreach($key as $k => $v) {
				if(the_user($k) != $v) return false;
			}
		}
		return true;
	}
	return false;
}

if(config('mod.installed')):

/** 依次创建 me_*() 函数 */
foreach (database('user') as $key) {
	$_key = substr($key, 5);
	$code = '
	function me_'.$_key.'($key = ""){
		$result = get_me("'.$key.'");
		if(!$key) return $result ?: null;
		elseif(isset($result[$key])) return $result[$key];
		elseif(strpos($key, "user_") !== 0){
			$key = "user_".$key;
			return isset($result[$key]) ? $result[$key] : null;
		}
	}';
	eval($code); //计算并运行代码
}
unset($code, $key, $_key); //释放变量

/** 设置默认用户级别 */
add_hook('user.add.set_level', function($arg){
	if(!is_logined() || !is_admin() || empty($arg['user_level']) || ($arg['user_level'] == config('user.level.admin') && me_id() != 1)){
		$arg['user_level'] = 1; //默认用户级别为 1
	}
	return $arg;
}, false);

/** 在更新或删除用户时检查操作权限 */
add_hook(array('user.update.check_permission', 'user.delete.check_permission'), function($arg){
	if(!is_logined()) return error(lang('user.notLoggedIn'));
	if(!empty($arg['user_id'])){
		$admin = config('user.level.admin');
		$denied = lang('mod.permissionDenied');
		if(get_user($arg['user_id'])){
			if(me_id() != user_id() && !is_admin() || (me_id() != 1 && user_level() == $admin))
				return error($denied);
			if(!empty($arg['user_level'])){
				if(($arg['user_level'] == $admin || user_level() == $admin) && (me_id() != 1 || user_id() == 1))
					return error($denied);
			}
		}else{
			return error(lang('mod.notExists', lang('user.label')));
		}
	}
}, false);

/** 在添加用户时检查用户名可用性 */
add_hook('user.add.check_name', function($arg){
	if(isset($arg['user_name'])){
		if(get_user(array('user_name'=>$arg['user_name']))) //尝试以传入的用户名获取用户
			return error(lang('user.usernameUnavailable'));
	}
}, false);

/** 更新用户名时检查其可用性 */
add_hook('user.update.check_name', function($arg){
	if(isset($arg['user_name'], $arg['user_id'])){
		if(get_user(array('user_name'=>$arg['user_name'])) && user_id() != $arg['user_id']) //用户存在则判断该用户是否为传入的用户
			return error(lang('user.usernameInvalid'));
	}
}, false);

/** 在添加或更新用户时检查用户名长度 */
add_hook(array('user.add.check_name_length', 'user.update.check_name_length'), function($arg){
	if(isset($arg['user_name'])){
		if(strlen($arg['user_name']) < config('user.name.minLength'))
			return error(lang('user.nameTooShort')); //太短
		if(strlen($arg['user_name']) > config('user.name.maxLength'))
			return error(lang('user.nameTooLong')); //太长
	}
}, false);

/** 在添加用户时检查密码长度 */
add_hook(array('user.add.check_password_length'), function($arg){
	if(isset($arg['user_password'])){
		if(strlen($arg['user_password']) < config('user.password.minLength'))
			return error(lang('user.passwordTooShort')); //太短
		if(strlen($arg['user_password']) > config('user.password.maxLength'))
			return error(lang('user.passwordTooLong')); //太长
	}
}, false);

/** 在添加用户时加密密码 */
add_hook('user.add.encrypt_password', function($arg){
	if(isset($arg['user_password'])){
		$arg['user_password'] = md5_crypt($arg['user_password']); //使用 MD5-Crypt 密码
	}else{
		$arg['user_password'] = md5_crypt(''); //不设置密码则使用空密码
	}
	return $arg;
}, false);

/** 在更新用户时检查密码可用性 */
add_hook('user.update.check_password', function($arg){
	if(isset($arg['user_password'])){
		if(!$arg['user_password']){       //如果传入密码为空
			unset($arg['user_password']); //则忽略不修改密码密码
		}else{ //检查密码长度
			if(strlen($arg['user_password']) < config('user.password.minLength'))
				return error(lang('user.passwordTooShort'));
			if(strlen($arg['user_password']) > config('user.password.maxLength'))
				return error(lang('user.passwordTooLong'));
			$arg['user_password'] = md5_crypt($arg['user_password']); //加密密码
		}
	}
	return $arg;
}, false);
endif;