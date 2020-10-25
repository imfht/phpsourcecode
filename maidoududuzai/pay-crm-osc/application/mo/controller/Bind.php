<?php

namespace app\mo\controller;

use \think\Db;
use \think\Session;

class Bind
{
	
	public $agent;

	public function __construct()
	{

	}

	public function wechat($agent_id)
	{
		$agent_id = authcode($agent_id, 'DECODE');
		if(empty($agent_id)) {
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
				if(0 != Db::name('agent')->where('openid', '=', $openid)->where('agent_id', '<>', $agent_id)->count()) {
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
					$this->agent = Db::name('agent')->where('agent_id', '=', $agent_id)->find();
					if(!$this->agent) {
						return '当前用户不存在';
					}
					if(!$this->agent['agent_status']) {
						return '当前用户已禁用';
					}
					Db::name('agent')->where('agent_id', '=', $agent_id)->update(['openid' => $openid]);
					Session::set('agent', $this->agent);
					return \befen\redirect('/mo/index/index');
				}
			}
		}
	}

}

