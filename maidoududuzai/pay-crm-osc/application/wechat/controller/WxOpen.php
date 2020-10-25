<?php

namespace app\wechat\controller;

use \think\Db;

import('WeChat.include', EXTEND_PATH, '.php');

class WxOpen
{

	public $config;
	public $Service;

	public function __construct()
	{
		$config = [
			'component_appid' => 'wx4855dabe01de977e',
			'component_token' => 'Tryyun',
			'component_appsecret' => 'd6c64b8a2c62d9349b8a5dfcd5e89b0d',
			'component_encodingaeskey' => 'KQEeeDYBjixiUBlzRtANhYSvvaejMtJnQeAYHmWXFYW',
			'cache_path' => TEMP_PATH . 'WeOpen'
		];
		$this->Service = new \WeOpen\Service($config);
	}

	public function log($content = '')
	{

		Tool::log($content, 'WeOpen');

	}

	public function auth()
	{
		$this->log(input('param.'));
		try {
			if(!($data = $this->Service->getComonentTicket())) {
				return 'Ticket event handling failed.';
			}
		} catch (\Exception $e) {
			echo $e->getMessage();
			exit();
		}
	}

	public function push($appid = '')
	{
		if(empty($appid)) {
			return;
		}
		$this->log(input('param.'));
	}

	public function test()
	{
		if(empty($merchant_id)) {
			$merchant_id = 1;
		}
		$authorizer = $this->authorizer($merchant_id);
		$User = $this->Service->instance('User', $authorizer['authorizer_appid']);
		$list = $User->getUserList();
		var_export($list);
		//$User = $User->getUserInfo('oPBWOjnX2LZH3p1u7wTiPOSTD2iw');
		//var_export($user);
	}

	public function authorizer($merchant_id = '')
	{
		if(empty($merchant_id)) {
			$merchant_id = 1;
		}
		$authorizer = Db::name('merchant_weixin')->where('merchant_id', '=', $merchant_id)->field('authorizer_appid, authorizer_expires, authorizer_access_token, authorizer_refresh_token')->find();
		$authorizer_appid = $authorizer['authorizer_appid'];
		if(_time() >= $authorizer['authorizer_expires']) {
			try {
				$refresh_authorizer = $this->Service->refreshAccessToken($authorizer['authorizer_appid'], $authorizer['authorizer_refresh_token']);
				$authorizer = [
					'authorizer_appid' => $authorizer_appid,
					'authorizer_expires' => $refresh_authorizer['expires_in'] + _time(),
					'authorizer_access_token' => $refresh_authorizer['authorizer_access_token'],
					'authorizer_refresh_token' => $refresh_authorizer['authorizer_refresh_token'],
				];
				Db::name('merchant_weixin')->where('merchant_id', '=', $merchant_id)->update($authorizer);
			} catch (\Exception $e) {
				echo $e->getMessage();
				exit();
			}
		}
		return $authorizer;
	}

	public function app_auth($merchant_id = '')
	{
		$merchant_return = input('param.merchant_return');
		try {
			$the_url = $this->Service->getAuthRedirect(url('app_auth_callback', ['merchant_id' => $merchant_id, 'merchant_return' => $merchant_return], null, true));
			return \befen\jump($the_url);
		} catch (\Exception $e) {
			echo $e->getMessage();
			exit();
		}
	}

	public function app_auth_callback($merchant_id = '')
	{
		$merchant_id = authcode($merchant_id, 'DECODE');
		try {
			$authorizer = $this->Service->getQueryAuthorizerInfo(input('param.auth_code'));
			Db::name('merchant_weixin')->where('merchant_id', '=', $merchant_id)->update([
				'authorizer_expires' => $authorizer['expires_in'] + _time(),
				'authorizer_appid' => $authorizer['authorizer_appid'],
				'authorizer_access_token' => $authorizer['authorizer_access_token'],
				'authorizer_refresh_token' => $authorizer['authorizer_refresh_token'],
			]);
			$merchant_return = input('param.merchant_return');
			if(empty($merchant_return)) {
				print_r($authorizer);
			} else {
				return \befen\jump($merchant_return);
			}
		} catch (\Exception $e) {
			echo $e->getMessage();
			exit();
		}
	}

}

