<?php
	namespace Frontend\Helpers\Auth\Weibo;
	use Modules\SuperAdmin\Models\Setting;

	$config = Setting::getSettings();
	require_once dirname(__FILE__) . '/saetv2.ex.class.php';
	define('WB_AKEY', $config['weibo_appkey']);
	define('WB_SKEY', $config['weibo_appsecret']);

	class Adapter {
		public static function auth(){
			$o = new \SaeTOAuthV2(WB_AKEY, WB_SKEY);
			$url = $o->getAuthorizeURL(WB_CALLBACK_URL, 'code', null, 'mobile');
//			echo $url;exit;
			header('Location:'.$url);
			exit;
		}
		public static function callback(){
			global $app;

			$o = new \SaeTOAuthV2( WB_AKEY , WB_SKEY );

			if (isset($_REQUEST['code'])) {
				$keys = array();
				$keys['code'] = $_REQUEST['code'];
				$keys['redirect_uri'] = WB_CALLBACK_URL;
				try {
					$token = $o->getAccessToken( 'code', $keys ) ;
				} catch (\OAuthException $e) {
					$app->log->error('oauth_error : ' . $e->getMessage());
					return array('error'=>$e->getMessage());
				}
			}

			if ($token) {
				$_SESSION['token'] = $token;
				$c = new \SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
				$uid = $token['uid'];
				$me = $c->show_user_by_id($uid);

				if($me['error']) {
					return $me;
				}

				unset($me['status']);

				return array(
					'third_id'			=>	$me['id'],
					'username'			=>	$me['screen_name'],
					'auth_type'			=>	'weibo',
					'avatar'			=>	$me['profile_image_url'],
					'access_token'		=>	$access_token
				);
			}else{
				return false;
			}
		}

		public function getClient($token){
			return new \SaeTClientV2(WB_AKEY, WB_SKEY, $token);
		}
	}
