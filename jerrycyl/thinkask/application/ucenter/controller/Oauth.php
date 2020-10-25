<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
// /ucenter/oauth/login/type/qq.html
namespace app\ucenter\controller;
use app\common\controller\Base;
class Oauth extends Base {
 public function _initialize()
    {
  
    
    }
    private function _getsnsconfig($type){
    	  switch ($type) {
			case 'qq':
			$think_sdk_qq = [
					'app_key' => getset('qq_login_app_id'),
				    'app_secret' => getset('qq_login_app_key'),
				    'callback' => 'http://'.$_SERVER["HTTP_HOST"].'/ucenter/oauth/callback/type/qq.html',
				    ];
				config('think_sdk_qq',$think_sdk_qq);
				break;
			case 'sina':
			$think_sdk_qq = [
					'app_key' => getset('sina_akey'),
				    'app_secret' => getset('sina_skey'),
				    'callback' => 'http://'.$_SERVER["HTTP_HOST"].'/ucenter/oauth/callback/type/sina.html',
				    ];
				config('think_sdk_sina',$think_sdk_qq);
				break;
			
			
			default:
				break;
		}  
    }
	
	public function login($type = null){
		empty($type) && $this->error(lang('parameter error'));
		session('login_http_referer',$_SERVER["HTTP_REFERER"]);
		$this->_getsnsconfig($type);
		$sns  = \thinksdk\ThinkOauth::getInstance($type);
		$this->redirect($sns->getRequestCodeURL());
	}

	public function callback($type = null, $code = null){
		(empty($type)) && $this->error(lang('parameter error'));
		if(empty($code)){
			$this->redirect(__ROOT__."/");
		}	
		$this->_getsnsconfig($type);
		$sns  = \thinksdk\ThinkOauth::getInstance($type);
		$extend = null;
		if($type == 'tencent'){
			$extend = array('openid' => input("openid"), 'openkey' => input("openkey"));
		}
		$token = $sns->getAccessToken($code , $extend);
		// show($token);
		//如果库中存在就直接登陆
		//====================================================逻辑一
		if($uid = $this->checkopenid($token['openid'])){
			//直接登陆
			 session('thinkask_uid',$uid);
             // 设置Cookie 有效期为 3600秒
             cookie('thinkask_uid',$uid);
			$this->success('登陆成功',url('/'));
			exit();
			
		}
		
		//获取当前登录用户信息
		if(is_array($token)){
			$qq = \thinksdk\ThinkOauth::getInstance('Qq', $token);
			//获取用户信息
       	    $data = $qq->call('user/get_user_info'); 
       	    if(!$data) $this->error('服务器异常，请稍后再试');
       	    $data['access_token'] = $token['access_token'];
       	    $data['expires_in'] = $token['expires_in'];
       	    $data['refresh_token'] = $token['refresh_token'];
       	    $data['openid'] = $token['openid'];
       	    $data['add_time'] = time();
  			
       	    //查看是否存在，存在直接登陆，不存在跳至绑定
       	   	//如果SESSION存在，就是帐号绑定到第三方，如果不存在就是第三方绑定到帐号 
       	   	if(session('thinkask_uid')){
       	   		//====================================================逻辑二
       	   		//绑定到帐号
       	   		$this->_bang_sns($data,$returnurl);
       	   	}else{
       	   		//====================================================逻辑三
       	   		session('sns_token',$data);
       	   		$this->success('授权成功',url('ucenter/oauth/bang_users'));
       	   	}
			
		}else{
			// $this->success(lang('login failed'),$this->_get_login_redirect());
		}
	}
	/**
	 * [checkopenid 检查当前的OPENID是否存在]
	 * @return [type] [description]
	 */
	private function checkopenid($openid){
		
		if($re = model('base')->getone('users_qq',['where'=>['openid'=>$openid]])){
			return $re['uid'];
		}else{
			return false;
		}
	}
	/**
	 * [locallogin 已绑定完凭，直接登陆]
	 * @return [type] [description]
	 */
	private function locallogin(){

	}
	/**
	 * [userlogin 用户选择直接用QQ号登陆]
	 * @return [type] [description]
	 */
	public function userlogin(){

	}
	/**
	 * [bang_users 从第三方绑定到本地帐号，需要填写本地测试]
	 * @return [type] [description]
	 */
	public function bang_users(){
		// show(session('sns_token'));
		$this->assign('sns_token',session('sns_token'));
		return $this->fetch();
	}
	/**
	 * [_bang_sns 绑定到第三方  本地帐户绑定到第三方]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	private function _bang_sns($data,$returnurl="/"){
		if($data){
			$data['uid'] = session('thinkask_uid');
			$data['add_time'] = time();
			model('base')->getadd('users_qq',$data);
			$this->success('绑定成功',$returnurl);
		}
	}

	
	
	

	
	
}