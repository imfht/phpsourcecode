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
 * Member class
 * $Author: Recho $license: http://www.recho.net/ $
 * $create time: 2011-11-01 18:50
 * $last update time: 2011-11-02 18:50 Recho $
 */
defined('IS_IN') or die('Include Error!');
class Session extends RcModel{
	
	private $session_id = '0';
	
	/**
	 * 创建一个SESSION
	 * @param unknown_type $userid	用户ID
	 * @param $user_name			用户名
	 * @return false:失败, $token:成功
	 */
	public function addSession( $userid, $user_name){

		$table = $this->T('www_session');
		$session_id = md5(uniqid(mt_rand(), true));
		$time = time();
		$date = serialize(array());
		$sql = "INSERT IGNORE INTO $table SET Sesskey='$session_id',Expiry='$time',User_id='$userid',User_name='$user_name',Email='$user_name',Data='$date'";
		odb::dbslave()->query( $sql);

		if( odb::dbslave()->affectedRows()){
			return $session_id . $this->genSessionKey( $session_id);
		}
		return false;
	}
	
	/**
	 * 检查SESSION是否登录状态
	 * @param unknown_type $session_key = $token
	 * @return false:未登录,array $sessionInfo:已登录,用户SESSION信息
	 */
	public function checkSession( $session_key){
	    if ( !empty($session_key) ){
            $tmp_session_id = substr($session_key, 0, 32);
            if ($this->genSessionKey($tmp_session_id) == substr($session_key, 32)){
                $session_id = $tmp_session_id;
            }
            else{
                return false;
            }
        }else{
        	return false;
        }
		$table = $this->T('www_session');
		$sql = "SELECT * FROM {$table} WHERE Sesskey='$session_id' LIMIT 1";
		$sessionInfo = odb::dbslave()->getOne( $sql, MYSQL_ASSOC);
		$time = time();
		if( !empty($sessionInfo) && $sessionInfo['User_id']>0 && $sessionInfo['Expiry']+3600*3>$time){
			odb::dbslave()->query( "UPDATE $table SET Expiry=$time WHERE Sesskey='$session_id' LIMIT 1");
			return $sessionInfo;
		}
		return false;
	}
	public function genSessionKey( $session_id){
		$ip = '';
		return sprintf('%08x', crc32(!empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT']  . $ip . $session_id :  $ip . $session_id));
	}
	
	/**
	 * 删除登录SESSION
	 * @param unknown_type $session_key
	 */
	public function delSessionKey( $session_key){
		if ( !empty($session_key) ){
            $tmp_session_id = substr($session_key, 0, 32);
            if ($this->genSessionKey($tmp_session_id) == substr($session_key, 32)){
                $session_id = $tmp_session_id;
            }
            else{
                return false;
            }
        }else{
        	return false;
        }
		$table = $this->T('www_session');
		$sql = "DELETE FROM $table WHERE Sesskey='$session_id' LIMIT 1";
		odb::dbslave()->query( $sql);
		if( odb::dbslave()->affectedRows()){
			return true;
		}else{
			return false;
		}
	}
}