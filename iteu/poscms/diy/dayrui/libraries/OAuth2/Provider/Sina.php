<?php



class OAuth2_Provider_Sina extends OAuth2_Provider {

	public $name = 'sina';
	public $human = '新浪微博';
	public $method = 'POST';
	public $uid_key	= 'uid';
	
	/**
     * 授权认证登录地址
     */
	public function url_authorize() {
		return 'https://api.weibo.com/oauth2/authorize';
	}
	
	/**
     * 授权认证访问地址
     */
	public function url_access_token() {
		return 'https://api.weibo.com/oauth2/access_token';
	}

	/**
     * 获取用户信息
     */
	public function get_user_info(OAuth2_Token_Access $token) {
		$url = 'https://api.weibo.com/2/users/show.json?'.http_build_query(array('access_token' => $token->access_token, 'uid' => $token->uid));
		$return = dr_catcher_data($url);
		$user = json_decode($return);
      	if (is_object($user) && array_key_exists('error', $user)) {
            throw new OAuth2_Exception($return);
        }
		// 返回统一的数据格式
		return array(
			'oid' => $user->id,
            'oauth' => $this->name,
			'avatar' => $user->profile_image_url,
			'nickname' => $user->name,
			'expire_at' => $token->expires,
			'access_token' => $token->access_token,
			'refresh_token'	=> $token->refresh_token
		);
	}
}