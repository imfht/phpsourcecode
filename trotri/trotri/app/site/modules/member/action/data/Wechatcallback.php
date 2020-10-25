<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace modules\member\action\data;

use library\DataAction;
use tfc\ap\Ap;
use tfc\ap\HttpCookie;
use tfc\saf\Cookie;
use tfc\saf\Cfg;
use libapp\Model;
use member\services\DataAccount;

/**
 * Wechatcallback class file
 * 第三方账号登录：微信联登，必须在微信客户端才有用
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Wechatcallback.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.member.action.data
 * @since 1.0
 */
class Wechatcallback extends DataAction
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$req    = Ap::getRequest();
		$cookie = new Cookie('cookie');

		$appid     = Cfg::getApp('appid', 'wechat', 'extlogin');
		$appsecret = Cfg::getApp('appsecret', 'wechat', 'extlogin');
		if ($cookie->get('state') !== $req->getParam('state')) {
			exit('The state does not match. You may be a victim of CSRF.');
		}

		$tokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?grant_type=authorization_code'
				  . '&appid='  . $appid
				  . '&secret=' . $appsecret
				  . '&code='   . $req->getParam('code');

		$resource = curl_init();
		curl_setopt($resource, CURLOPT_URL,               $tokenUrl);
		curl_setopt($resource, CURLOPT_HEADER,            0);
		curl_setopt($resource, CURLOPT_RETURNTRANSFER,    1);
		curl_setopt($resource, CURLOPT_NOSIGNAL,          1);
		curl_setopt($resource, CURLOPT_SSLVERSION,        CURL_SSLVERSION_TLSv1);

		$result = curl_exec($resource);
		if ($result === false) {
			$errNo = curl_errno($resource);
			$errMsg = curl_error($resource);
			curl_close($resource);

			echo '<h3>error:</h3>' . $errNo;
			echo '<h3>msg  :</h3>' . $errMsg;
			exit;
		}

		curl_close($resource);
		$user = json_decode($result);

		$openid = $user->openid;
		$mod = Model::getInstance('Account', 'member');
		$ret = $mod->extlogin(DataAccount::PARTNER_WECHAT, $openid);
		if ($ret['err_no'] === DataAccount::SUCCESS_LOGIN_NUM) {
			$httpReferer = HttpCookie::get('http_referer', 'index.php');
			HttpCookie::remove('http_referer');
			Ap::getResponse()->location($httpReferer);
		}
		else {
			Ap::getResponse()->location('index.php?r=member/show/login');
		}
	}
}
