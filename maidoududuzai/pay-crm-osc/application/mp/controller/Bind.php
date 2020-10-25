<?php

namespace app\mp\controller;

use \think\Db;
use \think\Session;

class Bind
{
	
	public $person;

	public function __construct()
	{

	}

	public function wechat($person_id)
	{
		$person_id = authcode($person_id, 'DECODE');
		if(empty($person_id)) {
			return '二维码已失效';
		}
		$WeChat = \app\common\WeChatConsole::init();
		$Oauth = $WeChat->Oauth();
		$code = input('param.code');
		if(empty($code)) {
			$the_url = \befen\get_url(true);
			return \befen\redirect($Oauth->getOauthRedirect($the_url, null, 'snsapi_base'));
		} else {
			$openid = null;
			try {
				$res = $Oauth->getOauthAccessToken();
			} catch (\Exception $e) {
				return $e->getMessage();
			}
			if(!empty($res['openid'])) {
				$openid = $res['openid'];
				if(0 != Db::name('store_person')->where('openid', '=', $openid)->where('person_id', '<>', $person_id)->count()) {
					return 'OpenID已绑定其他用户';
				}
				$User = $WeChat->load('User');
				try {
					$UserInfo = $User->getUserInfo($openid);
				} catch (\Exception $e) {
					return $e->getMessage();
				}
				if(!empty($UserInfo)) {
					if(0 == Db::name('wx_user')->where('openid', '=', $openid)->count()) {
						model('WxUser')->allowField(true)->save([
							'openid' => $UserInfo['openid'],
							'unionid' => $UserInfo['unionid'],
							'nickname' => $UserInfo['nickname'],
							'sex' => $UserInfo['sex'],
							'headimgurl' => $UserInfo['headimgurl'],
							'subscribe' => 1,
							'subscribe_time' => $UserInfo['subscribe_time'],
							'groupid' => $UserInfo['groupid'],
						]);
					} else {
						model('WxUser')->allowField(true)->save([
							'openid' => $UserInfo['openid'],
							'unionid' => $UserInfo['unionid'],
							'nickname' => $UserInfo['nickname'],
							'sex' => $UserInfo['sex'],
							'headimgurl' => $UserInfo['headimgurl'],
							'subscribe' => 1,
							'subscribe_time' => $UserInfo['subscribe_time'],
							'groupid' => $UserInfo['groupid'],
						], ['openid' => $openid]);
					}
				}
				if(!empty($openid)) {
					$this->person = Db::name('store_person')->where('person_id', '=', $person_id)->find();
					if(!$this->person) {
						return '当前用户不存在';
					}
					if(!$this->person['status']) {
						return '当前用户已禁用';
					}
					Db::name('store_person')->where('person_id', '=', $person_id)->update(['openid' => $openid]);
					Session::set('person', $this->person);
					return \befen\redirect('/mp/index/index');
				}
			}
		}
	}

}

