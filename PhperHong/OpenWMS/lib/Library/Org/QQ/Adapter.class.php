<?php
	namespace Org\QQ;
	require_once dirname(__FILE__) . '/qq.func.php';
	
	class Adapter {
		public static function auth(){
			$url = getQqLoginUrl(WB_AKEY);
//			echo $url;exit;
			header('Location:'.$url);
			exit;
		}
		public static function callback(){
			$access_token = getQqAccessToken(WB_AKEY, WB_SKEY);
			if(empty($access_token)) {
				return false;
			}
			$openid = getQqOpenid($access_token);
			if(empty($openid)) {
				return false;
			}
			$user = getQqUserInfo(WB_AKEY, $access_token, $openid);

			if ($user['ret'] == 0) {

				$str = $user['figureurl']; //'http://qzapp.qlogo.cn/qzapp/100265966/5200F1BAE50499BAF815F813D1FEDA2E/30';
				if(preg_match('#/([\w]*)/\d+$#', $str, $match)) {
					$out_user_id = $match[1];
					if(empty($user['nickname'])) {
						$user['nickname'] = 'QQ' . $out_user_id;
					}
					return array(
						'third_id'			=>	$out_user_id,
						'username'			=>	$user['nickname'],
						'auth_type'			=>	'qq',
						'avatar'			=>	$user['figureurl'],
						'access_token'		=>	$access_token
					);
				}
				return false;
			}else{
				return false;
			}
		}
	}
