<?php

ext::creatapp();

class ext{

	private static $_app;
	public static $user_realname=array();
	public static $role_name=array();
        public static $d_name=array();
        public static $school_name=array();

	public static function app() {
		return self::$_app;
	}

	public static function creatapp() {
		if(!is_object(self::$_app)) {
			self::$_app = site_init::instance();
		}
		return self::$_app;
	}
	
	public static function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
		$ckey_length = 4;
		$key = md5($key != '' ? $key : self::getglobal('authkey'));
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
	
		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);
	
		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);
	
		$result = '';
		$box = range(0, 255);
	
		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}
	
		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
	
		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}
	
		if($operation == 'DECODE') {
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc.str_replace('=', '', base64_encode($result));
		}
	}
	
	public static function getglobal($key, $group = null) {
		global $_G;
		$key = explode('/', $group === null ? $key : $group.'/'.$key);
		$v = &$_G;
		foreach ($key as $k) {
			if (!isset($v[$k])) {
				return null;
			}
			$v = &$v[$k];
		}
		return $v;
	}
	
	public static function synlogin($get, $post) {
		global $_G;

		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	
		$cookietime = 31536000;
		$user_id = intval($get['user_id']);
		if(($member = getuserbyuid($user_id))) {
			self::dsetcookie('auth', self::authcode("$member[password]\t$member[user_id]", 'ENCODE'), $cookietime);
		}
	}
	
	public static function synlogout($get, $post) {
		global $_G;

		if(!API_SYNLOGOUT) {
			return API_RETURN_FORBIDDEN;
		}

		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');

		dsetcookie('auth', '', -31536000);
	}
	
	public static function getuserbyuid($uid, $fetch = 0) {
		static $users = array();
		if(empty($users[$uid])) {
			$users[$uid] = DB::fetch_first('SELECT * FROM '.DB::table('users')." WHERE user_id='".$uid."' LIMIT 1");
		}
		//if(!isset($users[$uid]['self']) && $uid == self::getglobal('uid') && self::getglobal('uid')) {
		//	$users[$uid]['self'] = 1;
		//}
		return $users[$uid];
	}
    
	public static function getuserbyusername($user_name, $fetch = 0) {
		static $users = array();
		if(empty($users[$user_name])) {
			$users[$user_name] = DB::fetch_first('SELECT * FROM '.DB::table('users')." WHERE user_name='".$user_name."' LIMIT 1");
		}
		//if(!isset($users[$uid]['self']) && $uid == self::getglobal('uid') && self::getglobal('uid')) {
		//	$users[$uid]['self'] = 1;
		//}
		return $users[$user_name];
	}
    
	public static function check_user_exist($user_name,$modify_user_id = 0) {
        global $_G;
        $exist_user_id = $wheresql = '';
        if($modify_user_id){
            $wheresql = " AND user_id <> '".$modify_user_id."'";
        }
		if(!empty($user_name)) {
			$exist_user_id = DB::result_first('SELECT user_id FROM '.DB::table('users')." WHERE user_name='".$user_name."' ".$wheresql." LIMIT 1");
		}
		return $exist_user_id;
	}
	
	public static function dsetcookie($var, $value = '', $life = 0, $prefix = 1, $httponly = false) {
	
		global $_G;
	
		$config = $_G['config']['cookie'];
	
		$_G['cookie'][$var] = $value;
		$var = ($prefix ? $config['cookiepre'] : '').$var;
		$_COOKIE[$var] = $value;
	
		if($value == '' || $life < 0) {
			$value = '';
			$life = -1;
		}
	
		if(defined('IN_MOBILE')) {
			$httponly = false;
		}
	
		$life = $life > 0 ? getglobal('timestamp') + $life : ($life < 0 ? self::getglobal('timestamp') - 31536000 : 0);
		$path = $httponly && PHP_VERSION < '5.2.0' ? $config['cookiepath'].'; HttpOnly' : $config['cookiepath'];
	
		$secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
		if(PHP_VERSION < '5.2.0') {
			setcookie($var, $value, $life, $path, $config['cookiedomain'], $secure);
		} else {
			setcookie($var, $value, $life, $path, $config['cookiedomain'], $secure, $httponly);
		}
	}

	public static function getcookie($key) {
		global $_G;
		return isset($_G['cookie'][$key]) ? $_G['cookie'][$key] : '';
	}
	
	public static function getuseraccessbyuid($uid, $fetch = 0) {
		static $useraccess = array();
		if(empty($useraccess[$uid])) {
			$query = DB::query('SELECT * FROM '.DB::table('user_access')." WHERE user_id='".$uid."'");
			while ($value=DB::fetch($query)){
				$useraccess[$uid][$value['access_mod']][$value['access_action']][$value['access_do']]=$value['access_id'];
			}
		}
		//if(!isset($users[$uid]['self']) && $uid == self::getglobal('uid') && self::getglobal('uid')) {
		//	$users[$uid]['self'] = 1;
		//}
		return $useraccess[$uid];
	}
    
	public static function user_realname($user_id) {
		if(!isset(self::$user_realname[$user_id]) && $user_id) {
			self::$user_realname[$user_id]=DB::result_first('SELECT user_realname FROM '.DB::table('users')." WHERE user_id='$user_id' LIMIT 1");
		}
		return self::$user_realname[$user_id];
	}
    
    public static function role_name($role_id) {
		if(!isset(self::$role_name[$role_id]) && $role_id) {
			self::$role_name[$role_id]=DB::result_first('SELECT role_name FROM '.DB::table('user_role')." WHERE role_id='$role_id' LIMIT 1");
		}
		return self::$role_name[$role_id];
	}
    
        public static function d_name($d_id) {
		if(!isset(self::$d_name[$d_id]) && $d_id) {
			self::$d_name[$d_id]=DB::result_first('SELECT d_name FROM '.DB::table('dictionary')." WHERE d_id='$d_id' LIMIT 1");
		}
		return self::$d_name[$d_id];
	}
        
        public static function school_name($school_id) {
		if(!isset(self::$school_name[$school_id]) && $school_id) {
			self::$school_name[$school_id]=DB::result_first('SELECT school_name FROM '.DB::table('school')." WHERE school_id='$school_id' LIMIT 1");
		}
		return self::$school_name[$school_id];
	}  
    
    /**
     * 设置用户/角色 权限菜单函数
     *
     * @author HumingXu E-mail:huming17@126.com
     * @param int $type 菜单类型 1:角色权限菜单 2:用户权限菜单
     * @param string $menu_string 2:菜单menu_id字符串,逗号','分割
     * @param int $owner_id 角色或者用户的编号
     * @return TRUE/FALSE
     */
    public static function set_user_role_menu($type,$menu_string,$owner_id){
        $user_menu_array = array();
        $user_menu_count = 0;
        if(!empty($type) && !empty($menu_string) && !empty($owner_id)){
            switch ($type){
                case 1:
                    //DEBUG 更新原角色菜单结构 start ( 先删除原菜单结构,再插入新菜单结构 )
                    $sql_insert_new = "INSERT INTO ".DB::table('user_role_menu')."( menu_id, role_id) VALUES ";
                    $sql_delete_old = "DELETE FROM ".DB::table('user_role_menu')." WHERE role_id ='".$owner_id."'";
                    //DEBUG 更新原角色菜单结构 end
                    break;
                case 2:
                    $sql_insert_new = "INSERT INTO ".DB::table('user_role_menu')."( menu_id, user_id) VALUES ";
                    $sql_delete_old = "DELETE FROM ".DB::table('user_role_menu')." WHERE user_id ='".$owner_id."'";
                    break;
            }
            DB::query($sql_delete_old);
            $user_menu_array = array_unique(array_filter(explode(',', $menu_string)));
            $user_menu_count = count($user_menu_array);
            $dot = ',';
            for($i=0; $i <= $user_menu_count; $i++){
                if(!empty($user_menu_array[$i])){
                    if($i == $user_menu_count){
                        $dot = '';
                    }
                    $sql_insert_new .= "('".$user_menu_array[$i]."','".$owner_id."')".$dot;   
                }
            }
            DB::query($sql_insert_new);
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * 获取用户/角色 权限菜单函数
     *
     * @author HumingXu E-mail:huming17@126.com
     * @param int $type 菜单类型 1:角色权限菜单 2:用户权限菜单
     * @param int $owner_id 角色或者用户的编号
     * @param int $return_type 1:简单menu_id数组 2:数据库全数据 带层次关系
     * @return array $menu_array
     */
    public static function get_user_role_menu($type, $owner_id, $return_type){
        $menu_array = array();
        if(!empty($type) && !empty($owner_id) && !empty($return_type)){
            switch ($type){
                case 1:
                    $sql_menu_current = "SELECT menu_id from ".DB::table('user_role_menu')." WHERE role_id='".$owner_id."'";
                    break;
                case 2:
                    $sql_menu_current = "SELECT menu_id from ".DB::table('user_role_menu')." WHERE user_id='".$owner_id."'";
                    break;
            }
            $menu_current_query = DB::query($sql_menu_current);
            switch($return_type){
                case 1:
                    while($value = DB::fetch($menu_current_query)){
                        $menu_array[$value['menu_id']] = $value['menu_id'];
                    }
                    break;
                case 2:
                    while($value = DB::fetch($menu_current_query)){
                        $menu_array[$value['menu_id']] = $value['menu_id'];
                    }
                    $sql_owner_menu = "SELECT * FROM ".DB::table('common_menu')." WHERE menu_id IN (".dimplode($menu_array).") ORDER BY sort ASC";
                    $owner_menu_query = DB::query($sql_owner_menu);
                    while($value = DB::fetch($owner_menu_query)){
                        $owner_menu_array[$value['menu_id']] = $value;
                    }
                    $menu_array = self::format_menu($owner_menu_array);
                    break;
            }
        }
        return $menu_array;
    }
    
    /**
     * 递归一维菜单数组为树形数组 函数
     * 原理寻找终端节点，条件为有pid且无子节点，拼装于原数组，拼装后注销原节点
     * 
     * @author HumingXu E-mail:huming17@126.com
     * @param array $menu_array
     * @return array $menu_array_new
     */
    public static function format_menu($menu_array){
        $tmp_array = array();
        if(!empty($menu_array)){
            foreach($menu_array AS $key => $value){
                if(!self::chenk_menu_parent($value['menu_id'],$menu_array)){
                    $menu_array[$value['menu_pid']]['submenu'][$value['menu_id']] = $value;
                    $tmp_array[$value['menu_id']]=$value['menu_id'];
                }
            }
            foreach($tmp_array AS $key => $value){
                unset($menu_array[$key]);
            }
        }
        if(count($menu_array) == 1){
            $menu_array = $menu_array;
        }else{
            $menu_array = self::format_menu($menu_array);
        }
        return $menu_array;
    }
    
    /**
     * 递归一维菜单数组为树形数组 子函数 判断是否有子节点
     *
     * @author HumingXu E-mail:huming17@126.com
     * @param int $menu_id 菜单id
     * @return array TRUE/FALSE
     */
    public static function chenk_menu_parent($menu_id,$menu_array){
        $menu_array_new = array();
        if(!empty($menu_array)){
            foreach($menu_array AS $key => $value){
                if($menu_id == $value['menu_pid']){
                    return TRUE;
                }
            }
            return FALSE;
        }
        return $menu_array_new;
    }
    
    /**
     * 获取登陆用户菜单
     *
     * @author HumingXu E-mail:huming17@126.com
     * @param int $user_id 用户id
     * @param int $role_id 用户角色id
     * @return array $login_user_menu 登陆用户菜单
     */
    public static function login_user_menu($user_id, $role_id){
        $login_user_menu = array();
        $isexist_user_menu = $isexist_role_menu = 0;
        //DEBUG 是否存在用户菜单
        if(!empty($user_id)){
            $isexist_user_menu = DB::result_first("SELECT id FROM ".DB::table('user_role_menu')." WHERE user_id='".$user_id."' LIMIT 1");
            if(!empty($isexist_user_menu)){
                $login_user_menu = self::get_user_role_menu(2,$user_id,2);
                return $login_user_menu;
            }
        }
        
        //DEBUG 获取角色菜单
        if(!empty($role_id)){
            $isexist_role_menu = DB::result_first("SELECT id FROM ".DB::table('user_role_menu')." WHERE role_id='".$role_id."' LIMIT 1");
            if(!empty($isexist_role_menu)){
                $login_user_menu = self::get_user_role_menu(1,$role_id,2);
                 return $login_user_menu;
            }
        } 
    }

    /**
     * 获取校验当前访问用户是否有访问权限
     * 
     * @author HumingXu E-mail:huming17@126.com
     * @pram GET URL 当前URL地址
     * @return null 返回 跳转到前一个菜单
     */
    public static function auth_check(){
    	global $_G;
    	$refresh_time = 0;//毫秒
    	$location_href = '/';
    	$php_self = str_replace('/','',$_SERVER['PHP_SELF']);
    	$auth_url = $php_self;
    	if($_SERVER['QUERY_STRING']){
    		$auth_url .= '?'.$_SERVER['QUERY_STRING'];
    	}
    	if($_SERVER['HTTP_REFERER']){
    		$location_href = $_SERVER['HTTP_REFERER'];
    	}
    	$auth_url_md5 = md5($auth_url);
    	if(1 != $_G['login_user_menu_url_md5'][$auth_url_md5]){
    		echo '<script>alert("您没有权限访问,跳转中...");setTimeout("window.location.href =\''.$location_href.'\';", '.$refresh_time.');</script>';
    		die();
    	}
    }
}