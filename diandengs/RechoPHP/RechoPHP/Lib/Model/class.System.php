<?php
// +----------------------------------------------------------------------
// | RechoPHP [ WE CAN DO IT JUST Better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://recho.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: recho <diandengs@gmail.com>
// +----------------------------------------------------------------------

/**
 * System class
 * $Author: Recho $license: http://www.recho.net/ $
 * $create time: 2011-11-01 18:50
 * $last update time: 2011-11-02 18:50 Recho $
 */
defined('IS_IN') or die('Include Error!');
class System extends RcModel{
	private $secure = 'rechohaohsdf~ewr';
	
	/**
	 * 获取系统配置值(旧)
	 */
	public function configInfo(){
		$sql = "SELECT * FROM {$this->T('www_config')} LIMIT 1";
		$info = odb::dbslave()->getOne( $sql, MYSQL_ASSOC);
		return $info;
	}
	
	/**
	 * 系统配置设置(旧)
	 * @param unknown_type $info	健值项
	 * @return unknown
	 */
	public function configEdit( $info){
		if( !is_array( $info)) return false;
		foreach( $info as $key=>$value){
			$value = odb::db()->escape( $value);
			$sets .= "`$key`='$value',";
		}
		if( empty( $sets)) return false;
		$sets = substr( $sets, 0, -1);
		$sql = "UPDATE {$this->T('www_config')} SET $sets";
		odb::db()->query( $sql);
		return odb::db()->affectedRows();
	}
	
	/**
	 * 系统配置修改
	 * @param unknown_type $info
	 */
	public function optionsEdit( $info){
		if( !is_array( $info)) return false;
		$values = '';
		foreach( $info as $key=>$value){
			$value = odb::db()->escape( $value);
			$values .= "('$key','$value'),";
		}
		if( !empty($values)){
			$values = substr( $values, 0, -1);
			$sql = "INSERT IGNORE INTO {$this->T('www_options')}(optionName, optionValue)VALUES{$values} ON DUPLICATE KEY UPDATE optionValue=VALUES(optionValue)";
			odb::db()->query( $sql);
			return odb::db()->affectedRows();
		}
		return false;
	}
	
	/**
	 * 获取系统配置
	 */
	public function getOptions(){
		$sql = "SELECT * FROM {$this->T('www_options')}";
		$list = odb::dbslave()->getAll( $sql, MYSQL_ASSOC);
		$info = array();
		if( !empty( $list)){
			foreach( $list as $key=>$value){
				$info[$value['optionName']] = $value['optionValue'];
			}
		}
		return $info;
	}
	
	/**
	 * 管理员登录验证
	 * @param unknown_type $username	账号
	 * @param unknown_type $password	密码
	 * @return unknown	false | aInfo
	 */
	public function login( $username, $password){
		@session_start();
		if( strlen($username)>100 && strlen( $password)>16) return false;
		$password = md5($this->secure.$password);
		$sql = "SELECT * FROM {$this->T('www_admin')} WHERE `user`='$username' AND `password`='$password'";
		$info = odb::db()->getOne( $sql, MYSQL_ASSOC);
		if( !empty( $info)){
			$_SESSION['managelogin'] = true;
			$_SESSION['userInfo'] = $info;
			M('Auth')->unsetVerify();
			return true;
		}
		return false;
	}
	
	/**
	 * 退出登录
	 */
	public function logout(){
		@session_start();
		unset($_SESSION['managelogin']);
		unset($_SESSION['userInfo']);
	}
	
	/**
	 * 验证是否已登录
	 */
	public function isLogin(){
		@session_start();
		if( isset($_SESSION['managelogin']) && $_SESSION['managelogin']) return true;
		return false;
	}
	
	/**
	 * 密码修改
	 * @param unknown_type $user		用户名
	 * @param unknown_type $password	旧密码
	 * @param unknown_type $newPassword	新密码
	 */
	public function uppassword( $user, $password, $newPassword){
		if( strlen( $user)>35 || strlen( $password)>16 || strlen( $newPassword)>16){
			return false;
		}
		$user = odb::db()->escape( $user);
		$password = md5( $this->secure.$password);
		$newPassword = md5( $this->secure.$newPassword);
		$sql = "UPDATE {$this->T('www_admin')} SET password='$newPassword' WHERE user='$user' AND password='$password' LIMIT 1";
		odb::db()->query( $sql);
		return odb::db()->affectedRows();
	}
}