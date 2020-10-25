<?php

class OAuth2_Provider_Weixin extends OAuth2_Provider {

	public $name	= 'weixin';
	public $human	= 'weixin';
	public $method	= 'GET';
	public $client_id_key = 'appid';
	public $uid_key = 'appid';
	public $client_secret_key = 'secret';
	protected $scope = 'snsapi_login';

	/**
	 * 认证地址
	 */
	public function url_authorize() {
		return 'https://open.weixin.qq.com/connect/qrconnect';
	}

	/**
	 * ��Ȩ��֤���ʵ�ַ
	 */
	public function url_access_token() {
		return 'https://api.weixin.qq.com/sns/oauth2/access_token';
	}

	/**
	 * ��ȡ�û���Ϣ
	 */
	public function get_user_info(OAuth2_Token_Access $token) {
		// ʹ��Access Token����ȡ�û���OpenID
		$url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?'.http_build_query(array(
				'refresh_token' => $token->refresh_token,
				'grant_type' => 'refresh_token',
				'appid' => $this->client_id,
			));
		$response = dr_catcher_data($url);

		$me = json_decode($response);
		if (isset($me->errcode)) {
			throw new OAuth2_Exception($response);
		}
		// ʹ��Access Token��OpenID��ȡ�û���Ϣ
		$url = 'https://api.weixin.qq.com/sns/userinfo?'.http_build_query(array('access_token' => $token->access_token, 'openid' => $me->openid, 'oauth_consumer_key' => $this->client_id));
		$response = dr_catcher_data($url);
		$user = json_decode($response);
		if (isset($user->errcode)) {
			throw new OAuth2_Exception($response);
		}
		// ����ͳһ����ݸ�ʽ
		return array(
			'oid' => $me->openid,
			'oauth' => $this->name,
			'avatar' => $user->headimgurl,
			'nickname' => dr_weixin_emoji($user->nickname, 0),
			'expire_at' => $token->expires,
			'access_token' => $token->access_token,
			'refresh_token' => $token->refresh_token
		);
	}
}

