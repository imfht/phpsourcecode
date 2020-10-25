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
namespace home\Controller;
use Think\Controller;
use Think\Log;
use Think\Exception;
use \Org\QQ\Adapter;
use \Org\Weibo\Adapter1;
use \Org\Weixin\weixinAdapter;
use \Org\Util\MobileDetect;
class IndexController extends BaseController {
	public function index(){
	
	}
	/**
	 +----------------------------------------------------------
	 * 下载二维码
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	*/
    public function down_qr_code(){


        $filename = C('qr_code');

        $mime = 'application/force-download'; 
        header('Pragma: public'); // required 
        header('Expires: 0'); // no cache 
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0'); 
        header('Cache-Control: private',false); 
        header('Content-Type: '.$mime); 
        header('Content-Disposition: attachment; filename="qr_code.'.end(explode('.', $filename)).'"'); 
        header('Content-Transfer-Encoding: binary'); 
        header('Connection: close'); 
        readfile('http://'.$_SERVER['SERVER_NAME'].'/admin/upload/merchantqrcode/'.$filename); // push it out 
        exit(); 
    }
    /**
	 +----------------------------------------------------------
	 * 登录认证页面
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	*/
    public function login(){

    	if(!(preg_match('/^(UCWEB|Dalvik|Mozilla|iPad|iPhone)/', $_SERVER['HTTP_USER_AGENT']) || preg_match('/(AppleWebKit|Linux|Android|Mozilla)/', $_SERVER['HTTP_USER_AGENT']))){
			// app 请求被重定向之后进行屏蔽。
			die('unkown browser');
		}
		
		$gw_address 	= I('get.gw_address');			//获取路由访问地址
		$gw_port 		= I('get.gw_port');				//获取路由访问端口
		$gw_mac			= strtolower(I('get.gw_mac'));	//获取路由mac
		$mac 			= strtolower(I('get.mac'));		//获取用户设备mac
		$co 			= I('get.co');					//路由厂商名称
		$url 			= I('get.url');					//来路地址
		
        if (empty($gw_mac)){
        	return false;
        }
        //获取设备信息
        $detect = new MobileDetect;
        //设备类型
        $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'Tablet' : 'Phone') : 'computer');
        session('deviceType', $deviceType);
       
		

		if($gw_address) {
			session('gw_address', $gw_address);
		}

		if($gw_port) {
			session('gw_port', $gw_port);
		}

		if ($gw_mac){
			session('gw_mac', $gw_mac);
		}

		if($mac) {
			session('user_mac', $mac);
			session('user_mac_hash', md5($mac));
		}

		if($co) {
			session('co', $co);
		}

		if($url) {
			session('url', $url);
		}

        $client = DD('Client');
        
    	
		$user_info = $client->get_user_info($mac, array('status', 'end_date_time'));
        if (is_array($user_info) && $user_info['status'] == 1){
        	if ($user_info['end_date_time'] + 120 < time()){
        		//将该用户踢下线
	            $client->kick($mac);
        	}else{
        		//如果在线，则直接跳转到广告页面
	        	session('is_login', true);
	            redirect(U('Index/VerifyPage'));
        	}
        }
       

        //检测该时段是否为非上网时段
        $online_type = C('online_type');
        if (!empty($online_type)){
            //获取上网时段
            $online_type_time = explode('-', C('rest_online_times'));
            if (!empty($online_type_time[0]) && !empty($online_type_time[1])){
                //如果两个时间相等，代表全天有效
                if ($online_type_time[0] == $online_type_time[1]){
                    $timeb = true;
                }else{
                    //判断当前时间是否在上网时段中
                    $timeb = strtotime($online_type_time[0]) < time() && strtotime($online_type_time[1]) > time();
                }
            }else{
                //如果不是一个有效的时间段，则不限制时间
                $timeb = true;
            }
            //获取今天是星期几
            $today = date("w");
            //判断当天是否存在允许上网的日期中
            if ($online_type == 'everyday'){
                //每天
                $dayb = true;
            }else if($online_type == 'days' && in_array($today, array(1, 2, 3, 4, 5))){
                //工作日
                $dayb = true;
            }else if($online_type == 'weekend' && in_array($today, array(6, 0))){
                //周末
                $dayb = true;
            }
            if(!$dayb || !$timeb){
                exit('抱歉，商家设定该时间段不允许使用wifi');
            }
        }

        //判断是否为微信认证
        $mid_url = C('WEIXIN_HREF_URL').'/?weixintoken='.md5('weixin');
        if ($url == $mid_url){
            try{
                $smslog = DD('SmsLog');
                $rs = $smslog->weixin_verify();
                redirect(U('Index/VerifyPage'));
            }catch(Exception $e){
                exit($e->getMessage());
            }
        }
       	
        $count = 0;
        $str_verify = '';
        $virtual_verify = C('virtual_verify');
        $mobile_verify = C('mobile_verify');
        $akey_verify = C('akey_verify');
        $weixin_verify = C('weixin_verify');
        $weibo_verify = C('weibo_verify');
        $qq_verify = C('qq_verify');
        if ($virtual_verify == 1){
            $count ++;
            //如果存在虚拟短信认证，则关闭手机认证；
            $mobile_verify = 0;
            $str_verify = 'virtual_verify,';
        }
        if ($akey_verify == 1){
            $count ++;
        }
        if ($mobile_verify == 1){
            $count ++;
            $str_verify .= 'mobile_verify,';
        }
        if ($weixin_verify == 1){
            $count ++;
            $str_verify .= 'weixin_verify,';
        }
        if ($weibo_verify == 1){
            $count ++;
            $str_verify .= 'weibo_verify,';
        }
        if ($qq_verify == 1){
            $count ++;
            $str_verify .= 'qq_verify,';
        }
        $str_verify = rtrim($str_verify, ',');
        
        //查询该设备是否利用商家设置的几种方式登录过平台
        $user_list = $client->get_user_info_by_usermac($mac, $str_verify);
       	
        $is_old_user = false;
        //判断是否设置了第一次认证方式
        $auth_type = '';
        $tmp_auth_type_one = C('one_auth_type');

        $tmp_auth_type_two = C('two_auth_type');
       

        if (!empty($tmp_auth_type_one) && !$user_list[$tmp_auth_type_one]){
        	$auth_type = $tmp_auth_type_one; //应用第一次认证方式
        	session('auth_type_login', 1); //记录认证方式，用于广告跳转
        }else if (!empty($tmp_auth_type_two) && !$user_list[$tmp_auth_type_two]){
        	$auth_type = $tmp_auth_type_two; //应用第一次认证方式
        	session('auth_type_login', 2); //记录认证方式，用于认证完成后的跳转
        }else if (C('old_user_auth_type') == 1 && $user_list){
        	//是否开启老用户一键认证
        	session('fast_login_data', json_encode($user_list));
        	$is_old_user = true;
        }

     
  
    	$this->assign(array(
    		'title'				=> C('shop_name') . '-登录认证',
    		'shop_name'			=> C('shop_name'),
    		'homepage_logo'		=> C('homepage_logo'),
    		'homepage_banner'	=> C('homepage_banner'),
    		'virtual_verify'	=> C('virtual_verify'),
    		'akey_verify'		=> C('akey_verify'),
    		'mobile_verify'		=> C('mobile_verify'),
    		'weixin_verify'		=> C('weixin_verify'),
    		'weibo_verify'		=> C('weibo_verify'),
    		'qq_verify'			=> C('qq_verify'),
            'count'             => $count,
            'qr_code'           => C('qr_code'),
            'weixin_name'       => C('weixin_name'),
            'weixin_id'         => C('weixin_id'),
            'is_old_user'		=> $is_old_user,
            'auth_type'			=> $auth_type,
    	));
    	$this->display();
		
    }
    /**
     +----------------------------------------------------------
     * 快速登录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
    */
    public function fast_login(){
    	try {
    		$user_list = session('fast_login_data');
	    	if (empty($user_list)){
	    		exit('无法识别您的设备，请返回上一个页面并刷新');
	    	}
	    	$user_list = json_decode($user_list, true);
	    	$smslog = DD('SmsLog');
	    	if ($user_list['mobile_verify']){
				
            	$smslog->fast_mobile_verify($user_list['mobile']['username'], 'mobile_verify');

	    	}else if($user_list['virtual_verify']){
	    		$smslog->fast_mobile_verify($user_list['virtualmobile']['username'], 'virtual_verify');
	    	}else if($user_list['qq_verify']){

	    		$smslog->qq_verify(array(
	    			'third_id'      =>  $user_list['qq']['third_id'],
	    			'username'      =>  $user_list['qq']['username'],
	    			'avatar'        =>  $user_list['qq']['avatar'],
	    		));
	    	}else if($user_list['akey_verify']){
	    		$smslog->akey_verify();
	    	}else if($user_list['weixin_verify']){
	    		
	    		$smslog->weixin_verify();
	    	}else if($user_list['weibo']){

	    		$smslog->weibo_verify(array(
	    			'third_id'      =>  $user_list['weibo']['third_id'],
	    			'username'      =>  $user_list['weibo']['username'],
	    			'avatar'        =>  $user_list['weibo']['avatar'],
	    		));
	    	}else{
	    		exit('无法识别您的设备，请返回上一个页面并刷新');
	    	}
        	$gw_mac = session('gw_mac');
	    	redirect(U('Index/VerifyPage'));
    	} catch (Exception $e) {
    		exit($e->getMessage());
    	}
    	
    	
    }
    /**
     +----------------------------------------------------------
     * 路由心跳
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
    */
    public function ping(){
    	$gw_mac = I('get.gw_mac');
        $clientcount = I('get.clientcount');
    	$params = array(
    		'gw_mac'		=> I('get.gw_mac'),
    		'sys_uptime'	=> I('get.sys_uptime'),
    		'sys_memfree'	=> I('get.sys_memfree'),
    		'sys_load'		=> I('get.sys_load'),
    		'wifidog_uptime'=> I('get.wifidog_uptime'),
    		'check_time'	=> I('get.check_time'),
    		'wan_ip'		=> I('get.wan_ip'),
    		'clientcount'	=> empty($clientcount) ? I('get.client_count') : $clientcount,
    		'gw_address'	=> I('get.gw_address'),
    		'router_type'	=> I('get.router_type'),
    		'sv'			=> I('get.sv'),
    	);
    	$router = DD('Router');
    	$router->get_router_info_by_mac($params);

    	
    	echo 'Pong';
    }
    /**
     +----------------------------------------------------------
     * QQ认证
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
    */
    public function qq_auth(){

        define('WB_CALLBACK_URL', 'http://' . $_SERVER['HTTP_HOST'] . U('Index/qq_callback'));
        define('WB_AKEY', C('QQ_APP_ID'));
        define('WB_SKEY', C('QQ_APP_KEY'));

        Adapter::auth();
    }
    /**
     +----------------------------------------------------------
     * QQ回调页面
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
    */
    public function qq_callback(){
        define('WB_CALLBACK_URL', 'http://' . $_SERVER['HTTP_HOST'] . U('Index/qq_callback'));
        define('WB_AKEY', C('QQ_APP_ID'));
        define('WB_SKEY', C('QQ_APP_KEY'));
        $data = Adapter::callback();

        if($data === false || $data['error']) {
            echo '腾讯服务器没有正确返回您的用户信息，请重试！';
            exit;
            return true;
        }
        try{
            $smslog = DD('SmsLog');
            $rs = $smslog->qq_verify($data);
            redirect(U('Index/VerifyPage'));
        }catch(Exception $e){
            exit($e->getMessage());
        }
    }
    /**
     +----------------------------------------------------------
     * 微博认证页面
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
    */
    public function weibo_auth(){
        define('WB_CALLBACK_URL', 'http://' . $_SERVER['HTTP_HOST'] . U('Index/weibo_callback'));
        define('WB_AKEY', C('WEIBO_APP_KEY'));
        define('WB_SKEY', C('WEIBO_APP_SECRET'));
        Adapter1::auth();
    }
    /**
     +----------------------------------------------------------
     * QQ回调页面
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
    */
    public function weibo_callback(){
        define('WB_CALLBACK_URL', 'http://' . $_SERVER['HTTP_HOST'] . U('Index/weibo_callback'));
        define('WB_AKEY', C('WEIBO_APP_KEY'));
        define('WB_SKEY', C('WEIBO_APP_SECRET'));
        //关注当前用户相关微博
        $data = Adapter1::callback(C('weibo_name'));

        if($data === false || $data['error']) {
            echo '新浪微博服务器没有正确返回您的用户信息，请重试！';
            exit;
            return true;
        }
        try{
            $smslog = DD('SmsLog');
            $rs = $smslog->weibo_verify($data);
            redirect(U('Index/VerifyPage'));
        }catch(Exception $e){
            exit($e->getMessage());
        }
    }
    /**
     +----------------------------------------------------------
     * 一键登录
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
    */
    public function akey_verify(){
        try{

            $smslog = DD('SmsLog');
            $rs = $smslog->akey_verify();
            redirect(U('Index/VerifyPage'));
        }catch(Exception $e){
            exit($e->getMessage());
        }
    }
    /**
     +----------------------------------------------------------
     * 短信认证
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
    */
    public function send_sms(){
        $phonenumber = I('post.phonenumber');
        $return_data    = array();

        try{
            $smslog = DD('SmsLog');
            $rs = $smslog->sendSms($phonenumber);
            $return_data = array(
                'ret'           => 1,
                'msg'           => '验证码已发送，有效期为5分钟',
            );
        }catch(Exception $e){
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
            );
        }
        exit(json_encode($return_data));
    }
    /**
     +----------------------------------------------------------
     * 短信验证码检测
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
    */
    public function mobile_verify(){
        $phonenumber = I('post.phonenumber');
        $verifycode = I('post.verifycode');
        $type = I('post.type');
        $return_data    = array();

        try{
            $smslog = DD('SmsLog');
            $rs = $smslog->check_mobile_verify($phonenumber, $verifycode, $type);
           
            $return_data = array(
                'ret'           => 1,
                'msg'           => '验证成功',
                'url'           => U('Index/VerifyPage'),
            );
        }catch(Exception $e){
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
            );
        }
        exit(json_encode($return_data));

        
    }
    /**
     +----------------------------------------------------------
     * 虚拟短信认证
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
    */
    public function virtual_send_sms(){
        $phonenumber = I('post.phonenumber');
        $return_data    = array();

        try{
            $smslog = DD('SmsLog');
            $rs = $smslog->virtual_send_sms(session('gw_id'), $phonenumber);
            $return_data = array(
                'ret'           => 1,
                'msg'           => '上网验证码为【<font color="red" style="font-weight: bold;">'.$rs.'</font>】',
            );
        }catch(Exception $e){
            $return_data = array(
                'ret'   => 0,
                'msg'   => $e->getMessage(),
                'data'  => '',
            );
        }
        exit(json_encode($return_data));
    }
    public function control(){
   		$action = I('get.action');

   		if ($action == 'request'){
            $params = array(
                'gw_mac'    => strtolower(I('get.gw_mac')),
            
                'ssid'      => I('get.ssid'),
                'wan_ip'    => I('get.wan_ip'),
                'sv'        => I('get.sv'),
                'checktime' => I('get.checktime'),
                'timeout'   => I('get.timeout'),
                'enable'    => I('get.enable'),
                'apple'     => I('get.apple'),
                'nopop'     => I('get.nopop'),
                'whiteurl'  => I('get.whiteurl'),
                'whitemac'  => I('get.whitemac'),
            );
            //检测是否有任务
            $router_task = DD('RouterTask');
            $router_wifi_config = DD('RouterWifiConfig');
            $router_wifi_config->set_router_wifi_config($params); 
            $comm = $router_task->get_router_task($params);
            //echo 'task_id=1222;addjs:enable=1;time=5;url=http://auth.cnrouter.com/ad/show.js';
            //exit();
            if (!$comm){
                exit();
            }
            echo $comm;
            exit();
   		}else if($action == 'report'){
            $params = array(
                'gw_mac'    => strtolower(I('get.gw_mac')),
   
                'task_id'      => I('get.task_id'),
                'ret'    => I('get.ret'),
            );
            //检测是否有任务
            $router_task = DD('RouterTask');
            $router_task->set_router_task_status($params);
            
            exit();
        }
    }
    public function upgrade(){
        $params = array(
            'gw_mac'    => strtolower(I('get.gw_mac')),
           
            'router_type' => I('get.router_type'),
            'sv'        => I('get.sv'),
        );
        $router_task = DD('RouterTask');
        $comm = $router_task->get_upgrade_task($params);
        if (!$comm){
            exit();
        }
        echo $comm;
    }
    /*
    * 广告页
    */
    public function VerifyPage(){
        $gw_mac = session('gw_mac');
        $router = DD('Router');
		

        $ad_status = C('ad_status');
        $ad_list = C('ad_list');
        
       
        $is_login = false;
        
        if(session('is_login')) {

            $token = session('token');
            $gw_address = session('gw_address');
            $gw_port = session('gw_port');
            Log::record('广告页面用户MAC：'. session('user_mac'));
            if(empty($token)) {
                $client = DD('Client');
                $token = $client->get_user_token(session('user_mac'));
            }
            $is_login = true;
            if ($token && $gw_address && $gw_port) {

                $url = sprintf('http://%s:%s/wifidog/auth?token=%s', $gw_address, $gw_port, $token);
            }
        }

        $this->assign(array(
            'title'             => C('shop_name') . '-广告',
            'ad_list'           => $ad_list,
            'ad_times'          => C('ad_times'),
            'is_login'          => $is_login,
            'url'               => $url,
            'ad_status'			=> $ad_status,
        ));
        $this->display();
    }
    /*
    * 路由转向
    */
    public function portal(){
      
        $gw_mac = strtolower(I('get.gw_mac'));
        if (empty($gw_mac)){
        	$gw_mac = session('gw_mac');
        }
  
       	$router = DD('Router');
		
        $tmp = array(
        	'one_auth_href'	=> C('one_auth_href'),
        	'two_auth_href'	=> C('two_auth_href'),
        );
      	$href = C('href');
      	$auth_type_login = session('auth_type_login');
        if ($auth_type_login == 1 && !empty($tmp['one_auth_href'])){
        	$url = $tmp['one_auth_href'];
            $temp = substr($url, 0, 7);
            if ($temp != 'http://'){
                $url = 'http://' . $url;
            }
            redirect($url);
        }else if ($auth_type_login == 2 && !empty($tmp['two_auth_href'])){
        	$url = $tmp['two_auth_href'];
            $temp = substr($url, 0, 7);
            if ($temp != 'http://'){
                $url = 'http://' . $url;
            }
            redirect($url);
        }else if ($href == 'website'){
            redirect(U('Merchant/index'));
        }else if ($href == 'fixedwebsite'){
            $url = C('href_website');
            $temp = substr($url, 0, 7);
            if ($temp != 'http://'){
                $url = 'http://' . $url;
            }
            redirect($url);
        }else if ($href == 'sourcewebsite'){

            redirect(session('url'));
        }
	    
        exit('您现在可以上网了');
    }
    /*
    * 用户状态心跳
    */
    public function auth(){
        $token = I('get.token');

        $mac = strtolower(I('get.mac'));
        $incoming = I('get.incoming');
        $outgoing = I('get.outgoing');
        $stage = I('get.stage');
        $gw_id = strtolower(I('get.gw_id'));

        $ip = I('get.ip');
        $gw_mac = strtolower(I('get.gw_mac'));
   		session('gw_mac', $gw_mac);
        if (empty($token) || $token == '0'){
            Log::record('token:0:'.json_encode($_GET));
            exit(sprintf('Auth: %d', 0));
            return false;
        }
        //检测该路由是否授权
       
        $router = DD('Router');
		

       
       
       
        $client = DD('Client');  
        $ok = $client->check_user_token($mac, $token);
        
        $user_info = $client->get_user_info($mac, array('start_date_time'));
       	
        $ret = 1;
        if ($ok && !empty($token)){
            
            
        
            if ($stage == 'login'){
        
        		//更新用户状态
                $client->update_user_info($mac, 'status', 1);
                //更新用户上线时间
                $client->update_user_info($mac, 'start_date_time', time());
                Log::record('用户心跳页面，第一次登录，用户MAC：'. session('user_mac').', mac:'. $mac);
                
            	
                
            }else if ($stage == 'counters'){
                //更新用户流量
                $client->update_user_info($mac, 'incoming', $incoming);
                $client->update_user_info($mac, 'outgoing', $outgoing);
                Log::record('用户心跳页面，counters，用户MAC：'. session('user_mac').', mac:'. $mac);
                // 商家设定的超时时间
               	$merchant = DD('Merchant');
                $merchant_online_times = $merchant->get_merchant_online_times();
                
                
                if($merchant_online_times > 0) {
                
                    // 检测是否到达了上网限制时间
                    $start_date_time = intval($user_info['start_date_time']);
                    // 10s 补偿时间
                    $ret = intval(($merchant_online_times + $start_date_time + 10) >= time());
                    if(!$ret){
                         Log::record('超出商家限制时间:'.$merchant_online_times . ',用户开始时间：' .date('Y-m-d H:i:s', $start_date_time).',当前时间:'.date('Y-m-d H:i:s'));
                    }
                  
                }
     

            }else if($stage == 'logout'){
                Log::record('用户心跳页面，logout，用户MAC：'. session('user_mac').', mac:'. $mac);
                $ret = 0;
            }
        }else{
            $ret = 0;
        }

        // 由上面的状态值做统一处理
       
        if($ret == 1) {
            // 更新用户最后响应时间 
            $client->update_user_info($mac, 'end_date_time', time());
          	
            $client->set_list($mac);
        }else{
            Log::record('auth:0:用户数据'.json_encode($user_info) . '接收数据：' .json_encode($_GET));
            $client->kick($mac);
     
        }

  
        exit(sprintf('Auth: %d', $ret));
    }
    public function show(){
    	echo '123';
    }
   
}