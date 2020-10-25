<?php
// +----------------------------------------------------------------------
// | openWMS (开源wifi营销平台)
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://cnrouter.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/gpl-2.0.html )
// +----------------------------------------------------------------------
// | Author: PhperHong <phperhong@cnrouter.com>
// +----------------------------------------------------------------------
namespace admin\Model;
use Think\Model;
use Think\Exception;
use Think\Cache;
use Think\Log;

class SigninUserLogModel extends Model{
	protected $handler ;
	protected $cache;
 	function __construct() {
 		$this->handler = M('signin_user_log');
 		$this->cache   = Cache::getInstance();
 	}
 	/**
     +----------------------------------------------------------
     * 添加用户登录日志
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param a
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
 	public function add_signin_user_log($param){
          	$date = date('Y-m-d');
          	$count = $this->handler->where(array('mac_hash'=>$param['mac_hash'], 'date'=>$date))->count();
          	if ($count == 0){

               	$rs = $this->handler->add(array(
                    'mac_hash'     => $param['mac_hash'],
                    'date'         => date('Y-m-d'),
               	));
               	//新用户，给用户统计表中加1
               	$UserSigninLog = DD('UserSigninLog');
               	$UserSigninLog->add_signin_log();

               	if (session('deviceType') != ''){
               		$ClientTypeSigninLog = DD('ClientTypeSigninLog');
	               	$ClientTypeSigninLog->add_client_type_signinlog(array(
	                    'device_type'	=> session('deviceType'),
	               	));
               	}
               	if ($param['auth_type'] != ''){
               		$AuthTypeSigninLog = DD('AuthTypeSigninLog');
	               	$AuthTypeSigninLog->add_auth_type_signinlog(array(
	                    'auth_type'		=> $param['auth_type'],
	               	));
               	}


               	if (!$rs){
                    Log::record('signin_user_log表插入数据失败，sql'.$this->handler->getLastSql());
               	}
          	}else{
               	$rs = $this->handler->where(array('mac_hash'=>$param['mac_hash'], 'date'=>$date))->setInc('login_total', 1);
               	if (!$rs){
                    Log::record('signin_user_log表更新数据失败，sql'.$this->handler->getLastSql());
               	}
          	}
 	}
	
}