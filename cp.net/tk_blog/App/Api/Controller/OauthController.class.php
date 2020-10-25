<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
namespace Api\Controller;
use Common\Controller\HomebaseController;
class OauthController extends HomebaseController {
	//登录地址
	public function login($type = null){
		empty($type) && $this->error('参数错误');
		$_SESSION['login_http_referer']=$_SERVER["HTTP_REFERER"];
		//加载ThinkOauth类并实例化一个对象
		import("Org.ThinkSDK.ThinkOauth");
		$sns  = \ThinkOauth::getInstance($type);
		//跳转到授权页面
		redirect($sns->getRequestCodeURL());
	}

	//登录退出
	public function loginOut(){
		unset($_SESSION['user']);
		session_destroy();
		if (strrpos($_SERVER["HTTP_REFERER"],'/Comments/index/p') || strrpos($_SERVER["HTTP_REFERER"],'/msg')) {
			redirect(__ROOT__ . '/');
		}
		redirect($_SERVER["HTTP_REFERER"]);
	}

	//授权回调地址
	public function callback($type = null, $code = null){

		(empty($type)) && $this->error('参数错误');
		
		if(empty($code)){
			redirect(__ROOT__."/");
		}
	
		//加载ThinkOauth类并实例化一个对象
		import("Org.ThinkSDK.ThinkOauth");
		$sns  = \ThinkOauth::getInstance($type);
	
		//腾讯微博需传递的额外参数
		$extend = null;
		if($type == 'tencent'){
			$extend = array('openid' => I("get.openid"), 'openkey' => I("get.openkey"));
		}
		//请妥善保管这里获取到的Token信息，方便以后API调用
		//调用方法，实例化SDK对象的时候直接作为构造函数的第二个参数传入
		//如： $qq = ThinkOauth::getInstance('qq', $token);
		$token = $sns->getAccessToken($code , $extend);
		//获取当前登录用户信息
		if(is_array($token)){
			$user_info = A('Type', 'Event')->$type($token);
			if(!empty($_SESSION['oauth_bang'])){
				$this->_bang_handle($user_info, $type, $token);
			}else{
				$this->_login_handle($user_info, $type, $token);
			}
		}else{
			
			$this->success('登录失败！',$this->_get_login_redirect());
		}
	}


	public function bang($type=""){
		if(is_user_login()){
			empty($type) && $this->error('参数错误');
			//加载ThinkOauth类并实例化一个对象
			import("Org.ThinkSDK.ThinkOauth");
			$sns  = \ThinkOauth::getInstance($type);
			//跳转到授权页面
			$_SESSION['oauth_bang']=1;
			redirect($sns->getRequestCodeURL());
		}else{
			$this->error("您还没有登录！");
		}
		
		
	}

	//跳转到上一次浏览的地方
	private function _get_login_redirect(){
		return empty($_SESSION['login_http_referer'])?__ROOT__."/":$_SESSION['login_http_referer'];
	}
	
	//绑定第三方账号
	private function _bang_handle($user_info, $type, $token){
		
		$current_uid=sp_get_current_userid();
		$oauth_user_model = M('OauthUser');
		$type=strtolower($type);
		$find_oauth_user = $oauth_user_model->where(array("from"=>$type,"openid"=>$token['openid']))->find();
		$need_bang=true;
		if($find_oauth_user){
			
			if($find_oauth_user['uid']==$current_uid){
				$this->error("您之前已经绑定过此账号！",U('user/profile/bang'));exit;
			}else{
				$this->error("该帐号已被本站其他账号绑定！",U('user/profile/bang'));exit;
			}
			
		}
		
		if($need_bang){
			
			if($current_uid){
				//第三方用户表中创建数据
				$new_oauth_user_data = array(
						'from' => $type,
						'name' => $user_info['name'],
						'head_img' => $user_info['head'],
						'create_time' =>date("Y-m-d H:i:s"),
						'uid' => $current_uid,
						'last_login_time' => date("Y-m-d H:i:s"),
						'last_login_ip' => get_client_ip(0,true),
						'login_times' => 1,
						'status' => 1,
						'access_token' => $token['access_token'],
						'expires_date' => (int)(time()+$token['expires_in']),
						'openid' => $token['openid'],
				);
				$new_oauth_user_id=$oauth_user_model->add($new_oauth_user_data);
				if($new_oauth_user_id){
					$this->success("绑定成功！",U('user/profile/bang'));
				}else{
					$users_model->where(array("id"=>$new_user_id))->delete();
					$this->error("绑定失败！",U('user/profile/bang'));
				}
			}else{
				$this->error("绑定失败！",U('user/profile/bang'));
			}
			
		}
		
	}
	
	//登陆
	private function _login_handle($user_info, $type, $token){
		$oauth_user_model = M('OauthUser');
		$type=strtolower($type);
		$find_oauth_user = $oauth_user_model->where(array("from"=>$type,"openid"=>$token['openid']))->find();
		$need_register=true;
		if($find_oauth_user){
			//检测第三方 是否已经跟本地用户数据关联
			$find_user = M('Users')->where(array("uid"=>$find_oauth_user['users_uid']))->find();
			if($find_user){
				//已关联
				$need_register=false;
				if($find_user['user_status'] == '0'){
					$this->error('您可能已经被列入黑名单，请联系网站管理员！');exit;
				}else{
					$_SESSION["user"]=$find_user;
					redirect($this->_get_login_redirect());
				}
			}else{
				//没有关联 执行添加数据操作
				$need_register=true;
			}
		}
		
		if($need_register){
			//本地用户中创建对应一条数据
			$new_user_data = array(
					'nickname' => $user_info['name'],
					'face' => $user_info['head'],
					'last_login_time' => time(),
					'last_login_ip' => get_client_ip(0,true),
					'add_time' => time(),
					'user_status' => '1',
					"user_type"	  => '2',//会员
			);
			$oData = getImage($user_info['head'],1);
			if (!$oData['error']) {
				$new_user_data['face'] = $oData['save_path'];
			} else {
				$new_user_data['face'] = $user_info['head'];
			}
			$users_model=M("Users");
			$new_user_id = $users_model->add($new_user_data);
			
			if($new_user_id){
				//第三方用户表中创建数据
				$new_oauth_user_data = array(
						'from' => $type,
						'name' => $user_info['name'],
						'head_img' => $user_info['head'],
						'create_time' =>date("Y-m-d H:i:s"),
						'users_uid' => $new_user_id,
						'last_login_time' => date("Y-m-d H:i:s"),
						'last_login_ip' => get_client_ip(0,true),
						'login_times' => 1,
						'status' => 1,
						'access_token' => $token['access_token'],
						'expires_date' => (int)(time()+$token['expires_in']),
						'openid' => $token['openid'],
				);
				$new_oauth_user_id=$oauth_user_model->add($new_oauth_user_data);
				if($new_oauth_user_id){
					$new_user_data['uid']=$new_user_id;
					$_SESSION["user"]=$new_user_data;
					redirect($this->_get_login_redirect());
				}else{
					$users_model->where(array("uid"=>$new_user_id))->delete();
					$this->error("登陆失败",$this->_get_login_redirect());
				}
			}else{
				$this->error("登陆失败",$this->_get_login_redirect());
			}
			
		}
		
	}
}