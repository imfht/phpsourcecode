<?php

namespace app\mp\controller;

use \think\Db;

class Profile
{

	public $person;
	public $merchant;

	public function __construct()
	{
		$this->person = model('StorePerson')->checkLoginPerson();
		$this->merchant = Db::name('merchant')->where('merchant_id', '=', $this->person['merchant_id'])->field('merchant_id, merchant_name')->find();
	}

	public function index()
	{
		$value = Db::name('store_person sp')
			->join('store s', 's.store_id = sp.store_id', 'LEFT')
			->join('merchant m', 'm.merchant_id = sp.merchant_id', 'LEFT')
			->where('sp.person_id', '=', $this->person['person_id'])
			->field('sp.*, s.store_name, m.merchant_name')
			->find();
		include \befen\view();
	}

	public function bind_wechat($code = '')
	{
		$Oauth = \app\common\WeChatConsole::init()->Oauth();
		if(!$code) {
			$the_url = url('/mp/profile/bind_wechat', null, null, true);
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
			if(0 != Db::name('store_person')->where('openid', '=', $openid)->count()) {
				$status = 0;
				$message = 'OpenID已绑定其他用户';
			} else {
				Db::name('store_person')->where('person_id', '=', $this->person['person_id'])->update(['openid' => $openid]);
				$status = 1;
				$message = '微信绑定成功';
			}	
		}
		include \befen\view();
	}

}

