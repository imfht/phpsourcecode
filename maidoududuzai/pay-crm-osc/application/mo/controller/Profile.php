<?php

namespace app\mo\controller;

use \think\Db;
use \think\Session;

class Profile
{

	public $agent;
	public $AgentLevel;

	public function __construct()
	{
		$this->agent = model('Agent')->checkLoginAgent();
		$this->AgentLevel = model('AgentLevel')->getLevel();
	}

	public function index()
	{
		$value = Db::name('agent a')
			->join('agent_level al', 'a.level_id = al.level_id', 'LEFT')
			->where('a.agent_id', '=', $this->agent['agent_id'])
			->field('a.*, al.*')
			->find();
		include \befen\view();
	}

	public function account()
	{
		$value = Db::name('agent a')
			->join('agent_level al', 'a.level_id = al.level_id', 'LEFT')
			->where('a.agent_id', '=', $this->agent['agent_id'])
			->field('a.*, al.*')
			->find();
		include \befen\view();
	}

	public function cpasswd()
	{
		if(request()->isPost()) {
			$password = input('post.password');
			$new_password = input('post.new_password');
			$renew_password = input('post.renew_password');
			if($password != authcode($this->agent['password'], 'DECODE')) {
				return make_json(0, '原密码错误');
			}
			if(!$new_password) {
				return make_json(0, '请输入新密码!');
			}
			if($new_password != $renew_password) {
				return make_json(0, '两次密码输入不一致');
			}
			Db::name('agent')->where('agent_id', '=', $this->agent['agent_id'])->update([
				'password' => authcode($new_password, 'ENCODE'),
			]);
			Session::delete('agent');
			return make_json(1, '密码修改成功、前往登录页面');
		}
		include \befen\view();
	}

	public function bind_wechat($code = '')
	{
		$Oauth = \app\common\WeChatConsole::init()->Oauth();
		if(!$code) {
			$the_url = url('/mo/profile/bind_wechat', null, null, true);
			$the_url = $Oauth->getOauthRedirect($the_url, null, 'snsapi_base');
			return \befen\redirect($the_url);
		}
		try {
			$res = $Oauth->getOauthAccessToken();
		} catch (\Exception $e) {
			$res = null;
			$status = 0;
			$message = $e->getMessage();
		}
		if(!empty($res)) {
			$openid = $res['openid'];
			if(0 != Db::name('agent')->where('openid', '=', $openid)->count()) {
				$status = 0;
				$message = 'OpenID已绑定其他用户';
			} else {
				Db::name('agent')->where('agent_id', '=', $this->agent['agent_id'])->update(['openid' => $openid]);
				$status = 1;
				$message = '微信绑定成功';
			}	
		}
		include \befen\view();
	}

}

