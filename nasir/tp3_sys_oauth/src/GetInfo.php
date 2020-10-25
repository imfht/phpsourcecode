<?php
/**
 * 获取第三方用户信息类
 */
namespace Cp\Sys;
final class GetInfo{
	public static function getInstance($type,$token) {
		return self::$type($token);
	}

	//微信用户信息
	public static function weixin($token) {
		$weixin   = \Cp\Sys\Oauth::getInstance('weixin', $token);
		$data = $weixin->call('sns/userinfo');
		if (empty($data['ret'])) {
			if (!empty($data['errcode'])) {
				throw new \Exception("获取微信用户信息失败：errcode:{$data['errcode']} errmsg: {$data['errmsg']}");
			}
		}
		if($data['ret'] == 0){
			$userInfo['type'] = 'WEIXIN';
			$userInfo['name'] = $data['nickname'];
			$userInfo['nick'] = $data['nickname'];
			$userInfo['head'] = $data['headimgurl'];
			return $userInfo;
		} else {
			throw new \Exception("获取微信用户信息失败：{$data['msg']}");
		}
	}

	//QQ用户信息
	public static function qq($token){
		$qq   = \Cp\Sys\Oauth::getInstance('qq', $token);
		$data = $qq->call('user/get_user_info');

		if($data['ret'] == 0){
			$userInfo['type'] = 'QQ';
			$userInfo['name'] = $data['nickname'];
			$userInfo['nick'] = $data['nickname'];
			$userInfo['head'] = $data['figureurl_2'];
			return $userInfo;
		} else {
			throw new \Exception("获取腾讯QQ用户信息失败：{$data['msg']}");
		}
	}

	//新浪微博用户信息
	public static function sina($token){
		$sina = \Cp\Sys\Oauth::getInstance('sina', $token);
		$data = $sina->call('users/show', "uid={$sina->openid()}");

		if($data['error_code'] == 0){
			$userInfo['type'] = 'SINA';
			$userInfo['name'] = $data['name'];
			$userInfo['nick'] = $data['screen_name'];
			$userInfo['head'] = $data['avatar_large'];
			return $userInfo;
		} else {
			throw new \Exception("获取新浪微博用户信息失败：{$data['error']}");
		}
	}
}