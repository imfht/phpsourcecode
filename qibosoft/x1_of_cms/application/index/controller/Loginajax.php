<?php
namespace app\index\controller;
use app\common\model\User AS UserModel;
use app\common\controller\IndexBase;
use QCloud_WeApp_SDK\Auth\LoginService;
use QCloud_WeApp_SDK\Constants as Constants;
class Loginajax extends IndexBase{
	/**
	 * 常规登录
	 * @param string $fromurl 暂时没有使用 万一要用到呢
	 * @return array|mixed|\think\response\Json|void
	 */
	public function index($fromurl=''){
		if(!empty($this->user)){
			return $this->err_js('你已经登录了');
		}
		if(IS_POST){
			$data=get_post('post');
			if(empty($data['cookietime'])){
				$data['cookietime']=$this->webdb['login_time']?:3600*24*30;
			}
			$result=UserModel::login($data['username'],$data['password'],$data['cookietime']);
			if($result==0){
				return $this->err_js('当前用户不存在');
			}elseif($result==-1){
				return $this->err_js('密码不正确');
			}elseif(is_array($result)){
				$jump=$fromurl?urldecode($fromurl):iurl('index/index/index');
				return $this->ok_js('登录成功');
			}else{
				return $this->err_js('未知错误');
			}
		}
		if(strstr($this->fromurl,'index/login/quit')){
			$this->fromurl=url('index/index/index');
		}
		$this->assign('fromurl',urlencode(filtrate($fromurl?:$this->fromurl)));
		return $this->fetch();
	}
	/**
	 * 手机号登录和注册
	 * @return mixed
	 * @throws \Exception
	 */
	public function phone_reg(){
		if($this->webdb['forbid_normal_reg']){
			return $this->err_js('你可以选择QQ登录或微信登录');
		}
		if($this->user){
			return $this->err_js('你已经注册过了');
		}
		$this->get_hook('reg_by_hand_begin',$data=[]);
		hook_listen('reg_by_hand_begin',$data=[]);
		if(IS_POST){
			$data=get_post('post');
			if(!empty($data)){
				$array=explode(',','username,password,password2,email,mobphone,captcha,email_code,phone_code,weixin_code,fromurl');  //允许注册的字段
				foreach($data AS $key=>$value){
					if(!in_array($key,$array)){
						unset($data[$key]);
					}
				}
				if(isset($this->webdb['RegYz'])){
					$data['yz']=$this->webdb['RegYz'];
				}
				$data['money']=$this->webdb['regmoney'];
			}
			$phone_num=cache('phone_login'.$data['phone_code']);
			if(empty($data['phone_code'])){
				return $this->err_js('请输入验证码');
			}elseif(empty($phone_num)){
				return $this->err_js('验证码不正确');
			}
			cache('phone_login'.$data['phone_code'],null);
			$data['mobphone']=$phone_num;     //避免用户中途换号码
			if(UserModel::get_info($phone_num,'mobphone')){
				return $this->err_js('当前手机号已经注册过了,请直接登录即可');
			}
			$data['email']||$data['email']=substr($phone_num,7).rands(3).'@phone.cn';
			$data['password']||$data['password']=rands(6);
			$data['mob_yz']=1;
			$uid=UserModel::register_user($data); //注册帐号
			if($uid<2){
				return $this->err_js($uid);
			}
			$this->get_hook('reg_by_hand_end',$data,[],['uid'=>$uid]);
			hook_listen('reg_by_hand_end',$uid,$data);
			$result=UserModel::login($phone_num,'',3600*24*7,true,'mobphone');   //帐号同时实现登录
			if(is_array($result)){
				$url=murl('member/index/index');
				if($data['fromurl']&&!strstr($data['fromurl'],'index/login')){
					$url=$data['fromurl'];
				}
				$token=md5($result['uid'].$result['lastip'].$result['lastvist']);
				cache($token,"{$result['uid']}\t{$result['username']}\t".mymd5($result['password'],'EN')."\t",60);
				return $this->ok_js('注册成功');
				$this->success('注册成功',$url,$token);
			}else{
				return $this->err_js('注册失败');
			}
		}
		return $this->fetch();
	}
}
