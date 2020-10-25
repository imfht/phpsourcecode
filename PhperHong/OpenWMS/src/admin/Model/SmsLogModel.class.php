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
use \Org\Weibo\Adapter1;
class SmsLogModel extends Model{
	protected $handler ;
	protected $cache;
 	function __construct() {
 		$this->handler = M('sms_log');
 		$this->cache   = Cache::getInstance();
 	}
 	/**
	 +----------------------------------------------------------
	 * 发送短信
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $type
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function sendSms($phonenumber){
		
		if(empty($phonenumber)){
            throw new Exception("请输入手机号码", 1);
        }
		
        //判断是否在一分钟已经发送过短信
        $is_send_sms = $this->cache->get(session_id());
        if ($is_send_sms){
        	throw new Exception("您在一分钟内已经发送过一次，如果还未收到短信，请稍等或稍候再试。", 1);
        }
       

        $verifycode =  mt_rand(100011, 989989);
        $this->cache->set('verifycode'.md5($phonenumber), $verifycode, 300);

        $message = sprintf(C('SMS_MESSAGE'), $verifycode);
        $rs = sendSms($phonenumber, $message);
        if ($rs !== true){
        	Log::record('短信发送失败：'.$rs);
        	throw new Exception("发送失败，".$rs, 1);
        }
        $this->cache->set(session_id(), true, 60);
       
        //记录发送日志
        $param = array(
        	
        	'phonenumber'	=> $phonenumber,
        	'message'		=> $message,
        	'verifycode'	=> $verifycode,
        	'suc'			=> $rs !== true ? 0 : 1,
        	'last_send'		=> date('Y-m-d H:i:s'),
        );
        $rs2 = $this->add_sms_log($param);
        if (!$rs2){
        	Log::record('记录发送日志失败，参数'.json_encode($param));
        }
        return true;
	}
    /**
     +----------------------------------------------------------
     * 群发短信
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $type
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function send_sms($phone_list, $send_msg){
        if (empty($send_msg)){
            throw new Exception("请填写短信内容", 1);
        }
        if (empty($phone_list)){
            throw new Exception("号码列表为空", 1);
        }
        $reg = '/^(13[0-9]|15[0-9]|18[0-9])\d{8}$/';
        $phone_list = explode(',', $phone_list);
        $err_phone = '';
        $send_phone = array();
        foreach ($phone_list as $key => $value) {
            if (!preg_match($reg, $value)){
                $err_phone .= $value . ',';
            }
            if (!empty($value)){
                $send_phone[] = $value;
            }
            
        }
        if (!empty($err_phone)){
            $err_phone = rtrim($err_phone, ',');
            throw new Exception($err_phone.'号码格式错误', 1);
        }
        $merchant = DD('Merchant');
       

        $rs = sendSms($send_phone, $send_msg);
        if ($rs !== true){
            Log::record('短信发送失败：'.$rs);
            throw new Exception("发送失败，".$rs, 1);
        }
        
        $param = array();
        foreach ($send_phone as $key => $value) {
            $param[] = array(
               
                'phonenumber'   => $value,
                'message'       => $send_msg,
 
                'suc'           => $rs !== true ? 0 : 1,
                'type'          => 2,
                'last_send'     => date('Y-m-d H:i:s'),
            );
        }
        //记录发送日志
        
        $rs2 = $this->handler->addAll($param);
        if (!$rs2){
            Log::record('记录发送日志失败，参数'.json_encode($param));
        }
        return true;
    }
   
    /**
     +----------------------------------------------------------
     * 发送短信
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $type
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function virtual_send_sms($mid, $phonenumber){
        $merchant = DD('Merchant');
        if(empty($phonenumber)){
            throw new Exception("请输入手机号码", 1);
        }
        if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/", $phonenumber)){    
           throw new Exception("您输入的手机号码格式不正确，请检查", 1);     
        }

        //判断是否在一分钟已经发送过短信
        $is_send_sms = $this->cache->get(session_id());
        if ($is_send_sms){
            throw new Exception("您在一分钟内已经发送过一次，如果还未收到短信，请稍等或稍候再试。", 1);
        }
    
        $verifycode =  mt_rand(100011, 989989);
        $this->cache->set('verifycode'.md5($phonenumber), $verifycode, 300);

        $this->cache->set(session_id(), true, 60);
        //记录发送日志
        $message = sprintf(C('SMS_MESSAGE'), $verifycode);
        $param = array(
            'mid'           => $mid,
            'phonenumber'   => $phonenumber,
            'message'       => $message,
            'verifycode'    => $verifycode,
            'suc'           => 1,
            'last_send'     => date('Y-m-d H:i:s'),
            'type'          => 0,
        );
   
        $rs2 = $this->add_sms_log($param);
        if (!$rs2){
            Log::record('记录发送日志失败，参数'.json_encode($param));
        }
        
        return $verifycode;
    }
	/**
	 +----------------------------------------------------------
	 * 验证验证码
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $phonenumber
	 * @param $verifycode
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function check_mobile_verify($phonenumber, $verifycode, $type){
        if(empty($_SESSION['user_mac'])) {
            throw new Exception("无法获取用户手机MAC，请刷新页面重试", 1);
        }
        $verifycode_sys = $this->cache->get('verifycode'.md5($phonenumber));
        $msg = '';
        if ($type != 1 && !empty($verifycode_sys)){
            $msg = '网验证码为【<font color="red" style="font-weight: bold;">'.$verifycode_sys.'</font>】';
        }
		if(empty($phonenumber)){
            throw new Exception("请输入手机号码", 1);
        }
        if(empty($verifycode)){
            throw new Exception("请输入验证码 ".$msg, 1);
        }
        
        if (trim($verifycode) != $verifycode_sys){
        	throw new Exception("验证码错误，请确认 ".$msg, 1);
        }
        $auth_type = $type == 1 ? 'mobile_verify' : 'virtual_verify';
        //添加用户
        $data = array(
            'third_id'      =>  $auth_type.$phonenumber,
            'username'      =>  $phonenumber,
            'auth_type'     =>  $auth_type,
            'mac_hash'      =>  $_SESSION['user_mac_hash'],
        );

        $member = DD('Members');
        $uid = $member->syncUser($data);

        if (!$uid) {
        	throw new Exception("登录失败，请重试", 1);
        }
        
        //添加设备
        $client = DD('Client');
        $clientdata = array(
            'mid'           =>  $_SESSION['gw_id'],
            'mac_hash'      =>  $_SESSION['user_mac_hash'],
            'mac'           =>  $_SESSION['user_mac'],
            'user_id'       =>  $uid,
            'username'      =>  $data['username'],
            'third_id'      =>  $data['third_id'],
            'auth_type'     =>  $data['auth_type'],
            'router_mac'    =>  $_SESSION['gw_mac'],
        );
        $token = $client->add_client($clientdata);
        if ($token === false){
            throw new Exception("系统异常，设备无法记录", 1);
        }
        session('is_login', true);
        session('token', $token);
        //删除验证码
        $this->cache->rm(session_id());
        $this->cache->rm('verifycode'.md5($phonenumber));
        return $token;
	}
	/**
     +----------------------------------------------------------
     * 快速认证之短信认证
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function fast_mobile_verify($phonenumber, $type){
    	if(empty($_SESSION['user_mac'])) {
            throw new Exception("无法获取用户手机MAC，请刷新页面重试", 1);
        }
    	
        //添加用户
        $data = array(
            'third_id'      =>  $type.$phonenumber,
            'username'      =>  $phonenumber,
            'auth_type'     =>  $type,
            'mac_hash'      =>  $_SESSION['user_mac_hash'],
        );

        $member = DD('Members');
        $uid = $member->syncUser($data);

        if (!$uid) {
        	throw new Exception("登录失败，请重试", 1);
        }
        
        //添加设备
        $client = DD('Client');
        $clientdata = array(
            'mid'           =>  $_SESSION['gw_id'],
            'mac_hash'      =>  $_SESSION['user_mac_hash'],
            'mac'           =>  $_SESSION['user_mac'],
            'user_id'       =>  $uid,
            'username'      =>  $data['username'],
            'third_id'      =>  $data['third_id'],
            'auth_type'     =>  $data['auth_type'],
            'router_mac'    =>  $_SESSION['gw_mac'],
        );
        $token = $client->add_client($clientdata);
        if ($token === false){
            throw new Exception("系统异常，设备无法记录", 1);
        }
        session('is_login', true);
        session('token', $token);
        
        return $token;
    }
    /**
     +----------------------------------------------------------
     * QQ认证
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function qq_verify($data){
        //添加用户
        $data = array(
            'third_id'      =>  $data['third_id'],
            'username'      =>  $data['username'],
            'auth_type'     =>  'qq_verify',
            'avatar'        =>  $data['avatar'],
            'mac_hash'      =>  $_SESSION['user_mac_hash'],
        );

        $member = DD('Members');
        $uid = $member->syncUser($data);

        if (!$uid) {
            throw new Exception("登录失败，请重试", 1);
        }
        
        //添加设备
        $client = DD('Client');
        $clientdata = array(
            'mac_hash'      =>  $_SESSION['user_mac_hash'],
            'mac'           =>  $_SESSION['user_mac'],
            'user_id'       =>  $uid,
            'username'      =>  $data['username'],
            'third_id'      =>  $data['third_id'],
            'auth_type'     =>  $data['auth_type'],
            'router_mac'    =>  $_SESSION['gw_mac'],
        );
        $token = $client->add_client($clientdata);
        if ($token === false){
            throw new Exception("系统异常，设备无法记录", 1);
        }
        session('is_login', true);
        session('token', $token);
        return $token;
    }
    /**
     +----------------------------------------------------------
     * weibo认证
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function weibo_verify($data){
        
        $access_token = $data['access_token']['access_token'];
        //添加用户
        $data = array(
            'third_id'      =>  $data['third_id'],
            'username'      =>  $data['username'],
            'auth_type'     =>  'weibo_verify',
            'avatar'        =>  $data['avatar'],
            'mac_hash'      =>  $_SESSION['user_mac_hash'],
        );

        $member = DD('Members');
        $uid = $member->syncUser($data);

        if (!$uid) {
            throw new Exception("登录失败，请重试", 1);
        }
        
        //添加设备
        $client = DD('Client');
        $clientdata = array(
            'mac_hash'      =>  $_SESSION['user_mac_hash'],
            'mac'           =>  $_SESSION['user_mac'],
            'user_id'       =>  $uid,
            'username'      =>  $data['username'],
            'third_id'      =>  $data['third_id'],
            'auth_type'     =>  $data['auth_type'],
            'router_mac'    =>  $_SESSION['gw_mac'],
        );
        $token = $client->add_client($clientdata);
        if ($token === false){
            throw new Exception("系统异常，设备无法记录", 1);
        }

        session('is_login', true);
        session('token', $token);
        return $token;
    }
    public function weixin_verify(){
      
        $mac = session('user_mac');
        if (empty($mac)){
            throw new Exception("无法获取设备MAC", 1);
        }
        

        //添加用户
        $data = array(
            'third_id'      =>  'weixin'.md5($mac),
            'username'      =>  'weixin'.md5($mac),
            'auth_type'     =>  'weixin_verify',
            'mac_hash'      =>  $_SESSION['user_mac_hash'],
        );

        $member = DD('Members');
        $uid = $member->syncUser($data);

        if (!$uid) {
            throw new Exception("登录失败，请重试", 1);
        }
        
        //添加设备
        $client = DD('Client');
        $clientdata = array(
           
            'mac_hash'      =>  $_SESSION['user_mac_hash'],
            'mac'           =>  $mac,
            'user_id'       =>  $uid,
            'username'      =>  $data['username'],
            'third_id'      =>  $data['third_id'],
            'auth_type'     =>  $data['auth_type'],
            'router_mac'    =>  $_SESSION['gw_mac'],
        );
        $token = $client->add_client($clientdata);
        if ($token === false){
            throw new Exception("系统异常，设备无法记录", 1);
        }
        session('is_login', true);
        session('token', $token);
        return $token;
    }
    /**
     +----------------------------------------------------------
     * 一键登录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function akey_verify(){
        $mac = session('user_mac');
        if (empty($mac)){
            throw new Exception("无法获取设备MAC", 1);
        }
       

        //添加用户
        $data = array(
            'third_id'      =>  md5($mac),
            'username'      =>  md5($mac),
            'auth_type'     =>  'akey_verify',
            'mac_hash'      =>  $_SESSION['user_mac_hash'],
        );

        $member = DD('Members');
        $uid = $member->syncUser($data);

        if (!$uid) {
            throw new Exception("登录失败，请重试", 1);
        }
        
        //添加设备
        $client = DD('Client');
        $clientdata = array(
            'mac_hash'      =>  $_SESSION['user_mac_hash'],
            'mac'           =>  $mac,
            'user_id'       =>  $uid,
            'username'      =>  $data['username'],
            'third_id'      =>  $data['third_id'],
            'auth_type'     =>  $data['auth_type'],
            'router_mac'    =>  $_SESSION['gw_mac'],
        );
      
        $token = $client->add_client($clientdata);
        if ($token === false){
            throw new Exception("系统异常，设备无法记录", 1);
        }
        session('is_login', true);
        session('token', $token);
        return $token;
    }
    
	/**
	 +----------------------------------------------------------
	 * 添加短信日志
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param $param
	 +----------------------------------------------------------
	 * @return array
	 +----------------------------------------------------------
	*/
	public function add_sms_log($param){
		$rs = $this->handler->add($param);
        return $rs;
	}
    /**
     +----------------------------------------------------------
     * 获取短信发送日志
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param $param
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
    */
    public function get_sms_log_by_userid($pagenum, $pagelen, $sortkey, $reverse, $w){
        
        $where = array();
        if (!empty($w['phonenumber'])){
            $where['phonenumber'] = $w['phonenumber'];
        }
       
        $sortkey = empty($sortkey) ? 'id' : $sortkey;
        $reverse = empty($reverse) ? 'desc' : $reverse;
        $pagelen = intval($pagelen) == 0 ? 20 : $pagelen;
        $start = 0;
        if (intval($pagenum) > 0){
            $start = (intval($pagenum) - 1) * intval($pagelen);
        }
        $count = $this->handler->where($where)->count();
        $list = $this->handler->where($where)->order($sortkey . ' ' . $reverse)->limit($start. ',' . $pagelen)->select();

        return array('list'=>$list, 'count'=>$count);
    }
}