<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace addons\syslogin\controller;

use addons\syslogin\model\SyncLogin;
use think\facade\Session;

class Index extends \app\controller\front\Base {

	public function login() {
		$config = $this->getAddonsConfig();
		foreach ($config as $key => $value) {
			$config[$key] = json_decode($value, true);
		}
		$app = new \addons\syslogin\service\Application($config);
		$platform = $this->request->param('platform');
		return $this->redirect($app->$platform->getAuthorizeUrl());
	}

	public function callback() {
		$code = $this->request->param('code');
		if (!$code) {
			return $this->error("非法操作！");
		}
		$config = $this->getAddonsConfig();
		foreach ($config as $key => $value) {
			$config[$key] = json_decode($value, true);
		}
		$app = new \addons\syslogin\service\Application($config);
		$platform = $this->request->param('platform', 'wechat');
		$userInfo = $app->$platform->getUserInfo();

		Session::set("{$platform}-userinfo", $userInfo);
		$sync = SyncLogin::where(['platform' => $platform, 'openid' => $userInfo['openid']])->find();
		if ($sync) {
			if ($sync['uid']) {
				//已绑定用户直接登录
				SyncLogin::login($userInfo);
			} else {
				//未绑定用户跳转绑定用户
				return $this->redirect('/addons/syslogin/index/bind/platform/' . $platform);
			}
		} else {
			SyncLogin::register($userInfo);
			//未绑定用户跳转绑定用户
			return $this->redirect('/addons/syslogin/index/bind/platform/' . $platform);
		}
	}

	public function bind() {
		$platform = $this->request->param('platform', 'wechat');

		$userinfo = Session::get("{$platform}-userinfo");

		$this->data = [
			'userinfo' => $userinfo['userinfo'],
			'platform' => $platform,
		];
		return $this->fetch();
	}
}
