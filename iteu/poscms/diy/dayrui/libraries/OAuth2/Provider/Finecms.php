<?php


class OAuth2_Provider_Finecms extends OAuth2_Provider {

	public $name	= 'finecms';
	public $human	= 'FineCMS官方';
	public $method	= 'POST';
	public $uid_key	= 'uid';
	public $client_id_key = 'oauth_token';
	
	/**
     * 授权认证登录地址
     */
	public function url_authorize() {
		return 'http://oauth.finecms.net/authorize.php';
	}
	
	/**
     * 授权认证访问地址
     */
	public function url_access_token() {
		return 'http://oauth.finecms.net/request_token.php';
	}

	/**
     * 获取用户信息
     */
	public function get_user_info(OAuth2_Token_Access $token) {
		$url = 'http://oauth.finecms.net/member.php?'.http_build_query(array('token' => $token->access_token, 'uid' => $token->uid));
		$return = file_get_contents($url);
		$user = json_decode($return);
      	if (array_key_exists('error', $user)) throw new OAuth2_Exception($user['error']);
		// 返回统一的数据格式
		return array(
			'oid' => $user['openid'],
            'oauth' => $this->name,
			'avatar' => $user['avatar'],
			'nickname' => $user['username'],
			'expire_at' => $token->expires,
			'access_token' => $token->access_token,
			'refresh_token'	=> $token->refresh_token
		);
	}
}