<?php

/**
 * Wikin! [ Discuz!应用专家，维清互联旗下最新品牌 ]
 *
 * Copyright (c) 2011-2099 http://www.wikin.cn All rights reserved.
 *
 * Author: wikin <wikin@wikin.cn>
 *
 * $Id: OAuth.class.php 2015-5-13 15:29:16Z $
 */
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class OAuth {

	private $_oAuthAuthorizeURL_V2 = 'https://graph.qq.com/oauth2.0/authorize';
	private $_accessTokenURL_V2 = 'https://graph.qq.com/oauth2.0/token';
	private $_openIdURL_V2 = 'https://graph.qq.com/oauth2.0/me';
	private $_getUserInfoURL_V2 = 'https://graph.qq.com/user/get_user_info';

	const RESPONSE_ERROR = 999;

	public function __construct($AppId = '', $AppKey = '', $isapi = false) {
		global $_G;

		$AppId = !empty($AppId) ? $AppId : $_G['cache']['plugin']['qq']['appid'];
		$AppKey = !empty($AppKey) ? $AppKey : $_G['cache']['plugin']['qq']['appkey'];

		$this->setAppkey($AppId, $AppKey);

		if ($isapi) {
			if (!$this->_appKey) {
				throw new Exception('AppId Invalid', __LINE__);
			}
		} else {
			if (!$this->_appKey || !$this->_appSecret) {
				throw new Exception('AppId Or AppKey Invalid', __LINE__);
			}
		}
	}

	private static function _dfsockopen($api, $get = array(), $post = array()) {
		global $_G;
		return dfsockopen($api . http_build_query($get), 0, $post, '', false);
	}

	private static function _convert($post) {
		if (is_array($post)) {
			foreach ($post as $k => $v) {
				$post[$k] = diconv($v, 'UTF-8', CHARSET);
			}
		} else {
			$post = diconv($post, 'UTF-8', CHARSET);
		}

		return $post;
	}

	protected function setAppKey($appKey, $appSecret) {
		$this->_appKey = $appKey;
		$this->_appSecret = $appSecret;
	}

	public function getOAuthAuthorizeURL($redirect_uri) {
		$params = array(
			'response_type' => 'code',
			'client_id' => $this->_appKey,
			'redirect_uri' => $redirect_uri,
			'state' => md5(FORMHASH),
			'scope' => 'get_user_info',
		);

		return $this->_oAuthAuthorizeURL_V2 . '?' . http_build_query($params);
	}

	public function getOpenId($redirect_uri, $code) {
		$params = array(
			'grant_type' => 'authorization_code',
			'client_id' => $this->_appKey,
			'redirect_uri' => $redirect_uri,
			'client_secret' => $this->_appSecret,
			'code' => $code
		);
		$response = $this->_dfsockopen($this->_accessTokenURL_V2 . '?', $params);
		parse_str($response, $result);

		if ($result['access_token'] && $result['refresh_token']) {
			$params = array(
				'access_token' => $result['access_token']
			);
			$response = $this->callback($this->_dfsockopen($this->_openIdURL_V2 . '?', $params));

			if ($response->openid) {
				$result = array(
					'openid' => $response->openid,
					'access_token' => $result['access_token']
				);
				return $result;
			} else {
				$response->error = $response->error ? $response->error : self::RESPONSE_ERROR;
				throw new Exception($response->error, __LINE__);
			}
		} else {
			$result = $this->callback($response);

			$result->error = $result->error ? $result->error : self::RESPONSE_ERROR;
			throw new Exception($result->error, __LINE__);
		}
	}

	public function getUserInfo($openId, $accessToken) {

		$params = array(
			'access_token' => $accessToken,
			'oauth_consumer_key' => $this->_appKey,
			'openid' => $openId,
			'format' => 'json'
		);

		$response = $this->_dfsockopen($this->_getUserInfoURL_V2 . '?', $params);
		$data = json_decode($response, true);

		if (isset($data['ret']) && $data['ret'] == 0) {
			return $data;
		} else {
			throw new Exception($data['msg'], $data['ret']);
		}
	}

	public function callback($response) {
		if (strpos($response, "callback") === false) {
			return array();
		}
		$lpos = strpos($response, "(");
		$rpos = strrpos($response, ")");
		$response = substr($response, $lpos + 1, $rpos - $lpos - 1);
		return json_decode($response);
	}

}

?>