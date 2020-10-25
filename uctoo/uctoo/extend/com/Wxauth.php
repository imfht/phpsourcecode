<?php
/**
 * 微信oAuth认证示例
 */
namespace com;
use think\Controller;
load_trait('controller/Jump');
class Wxauth {
	private $options;
	public $open_id;
	public $wxuser;
	
    use \traits\controller\Jump;
	public function __construct($options){
		$this->options = $options;
		$this->wxoauth();
	}
	
	public function wxoauth(){
		$scope = 'snsapi_base';
		$code = isset($_GET['code'])?$_GET['code']:'';
		$token_time = isset($_SESSION['token_time'])?$_SESSION['token_time']:0;
		if(!$code && isset($_SESSION['open_id']) && isset($_SESSION['user_token']) && $token_time>time()-3600)
		{
			if (!$this->wxuser) {
				$this->wxuser = $_SESSION['wxuser'];
			}
			$this->open_id = $_SESSION['open_id'];
			return $this->open_id;
		}
		else
		{

			$options = array(
					'token'=>$this->options["token"], //填写你设定的key
                    'encodingaeskey'=>$this->options["encodingaeskey"], //填写加密用的EncodingAESKey
					'appid'=>$this->options["appid"], //填写高级调用功能的app id
					'appsecret'=>$this->options["appsecret"] //填写高级调用功能的密钥
			);
			$we_obj = new TPWechat($options);
			if ($code) {
				$json = $we_obj->getOauthAccessToken();
				if (!$json) {
					unset($_SESSION['wx_redirect']);
					die('获取用户授权失败，请重新确认');
				}
				$_SESSION['open_id'] = $this->open_id = $json["openid"];
				$access_token = $json['access_token'];
				$_SESSION['user_token'] = $access_token;
				$_SESSION['token_time'] = time();
				$userinfo = $we_obj->getUserInfo($this->open_id);
				if ($userinfo && !empty($userinfo['nickname'])) {
					$this->wxuser = array(
							'openid'=>$this->open_id,
							'nickname'=>$userinfo['nickname'],
							'sex'=>intval($userinfo['sex']),
							'province'=>$userinfo['province'],
                            'city'=>$userinfo['city'],
							'headimgurl'=>$userinfo['headimgurl']
					);
				} elseif (strstr($json['scope'],'snsapi_userinfo')!==false) {
					$userinfo = $we_obj->getOauthUserinfo($access_token,$this->open_id);
					if ($userinfo && !empty($userinfo['nickname'])) {
						$this->wxuser = array(
                            'openid'=>$this->open_id,
                            'nickname'=>$userinfo['nickname'],
                            'sex'=>intval($userinfo['sex']),
                            'province'=>$userinfo['province'],
                            'city'=>$userinfo['city'],
                            'headimgurl'=>$userinfo['headimgurl']
						);
					} else {
						return $this->open_id;
					}
				}
				if ($this->wxuser) {
					$_SESSION['wxuser'] = $this->wxuser;
					$_SESSION['open_id'] =  $json["openid"];
					unset($_SESSION['wx_redirect']);
					return $this->open_id;
				}
				$scope = 'snsapi_userinfo';
			}
			if ($scope=='snsapi_base') {
				$url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				$_SESSION['wx_redirect'] = $url;
			} else {
				$url = $_SESSION['wx_redirect'];
			}
			if (!$url) {
				unset($_SESSION['wx_redirect']);
				die('获取用户授权失败');
			}
			$oauth_url = $we_obj->getOauthRedirect($url,"wxbase",$scope);
            $this->redirect($oauth_url);
		}
	}
}
//$options = array(
//		'token'=>'tokenaccesskey', //填写你设定的key
//		'appid'=>'wxdk1234567890', //填写高级调用功能的app id, 请在微信开发模式后台查询
//		'appsecret'=>'xxxxxxxxxxxxxxxxxxx', //填写高级调用功能的密钥
//);
//$auth = new Wxauth($options);
//var_dump($auth->wxuser);
