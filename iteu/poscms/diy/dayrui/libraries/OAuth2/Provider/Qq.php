<?php


class OAuth2_Provider_Qq extends OAuth2_Provider {

	public $name	= 'qq';
	public $human	= 'QQ';
	public $method	= 'POST';
	public $uid_key = 'openid';
	protected $scope = 'get_user_info,add_pic_t,add_t';
	
	/**
     * ��Ȩ��֤��¼��ַ
     */
	public function url_authorize() {
		return 'https://graph.qq.com/oauth2.0/authorize';
	}
	
	/**
     * ��Ȩ��֤���ʵ�ַ
     */
	public function url_access_token() {
		return 'https://graph.qq.com/oauth2.0/token';
	}
	
	/**
     * ��ȡ�û���Ϣ
     */
	public function get_user_info(OAuth2_Token_Access $token) {
		// ʹ��Access Token����ȡ�û���OpenID
		$url = 'https://graph.qq.com/oauth2.0/me?'.http_build_query(array('access_token' => $token->access_token));
		$response = dr_catcher_data($url);
        if (strpos($response, 'callback') !== false) {
            $lpos = strpos($response, '(');
            $rpos = strrpos($response, ')');
            $response = substr($response, $lpos + 1, $rpos - $lpos -1);
        }
        $me = json_decode($response);
        if (isset($me->error)) {
            throw new OAuth2_Exception($response);
        }
		// ʹ��Access Token��OpenID��ȡ�û���Ϣ
        $url = 'https://graph.qq.com/user/get_user_info?'.http_build_query(array('access_token' => $token->access_token, 'openid' => $me->openid, 'oauth_consumer_key' => $this->client_id));
        $response = dr_catcher_data($url);
		$user = json_decode($response);
	    if (isset($user->ret) && $user->ret != 0) {
            throw new OAuth2_Exception($response);
        }
		// ����ͳһ����ݸ�ʽ
		return array(
			'oid' => $me->openid,
            'oauth' => $this->name,
			'avatar' => $user->figureurl_1,
			'nickname' => $user->nickname,
			'expire_at' => $token->expires,
			'access_token' => $token->access_token,
			'refresh_token' => $token->refresh_token
		);
	}
}