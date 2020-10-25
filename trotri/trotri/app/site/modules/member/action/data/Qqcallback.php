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
use system\services\Options;

/**
 * Qqcallback class file
 * 第三方账号登录：QQ联登
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Qqcallback.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.member.action.data
 * @since 1.0
 */
class Qqcallback extends DataAction
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$req    = Ap::getRequest();
		$cookie = new Cookie('cookie');

		$appid    = Cfg::getApp('appid', 'qq', 'extlogin');
		$appkey   = Cfg::getApp('appkey', 'qq', 'extlogin');
		$callback = Options::getSiteUrl() . '/index.php?r=member/data/qqcallback';
		if ($cookie->get('state') !== $req->getParam('state')) {
			exit('The state does not match. You may be a victim of CSRF.');
		}

		$tokenUrl = 'https://graph.qq.com/oauth2.0/token?grant_type=authorization_code'
				  . '&client_id='     . $appid
				  . '&redirect_uri='  . urlencode($callback)
				  . '&client_secret=' . $appkey
				  . '&code='          . $req->getParam('code');

		$response = file_get_contents($tokenUrl);
		if (strpos($response, 'callback') !== false) {
			$lpos = strpos($response, '(');
			$rpos = strrpos($response, ')');
			$response = substr($response, $lpos + 1, $rpos - $lpos -1);
			$msg = json_decode($response);
			if (isset($msg->error)) {
				echo '<h3>error:</h3>' . $msg->error;
				echo '<h3>msg  :</h3>' . $msg->error_description;
				exit;
			}
		}

		$params = array();
		parse_str($response, $params);

		$graphUrl = 'https://graph.qq.com/oauth2.0/me?access_token=' . $params['access_token'];
		$str  = file_get_contents($graphUrl);
		if (strpos($str, 'callback') !== false) {
			$lpos = strpos($str, '(');
			$rpos = strrpos($str, ')');
			$str  = substr($str, $lpos + 1, $rpos - $lpos -1);
		}

		$user = json_decode($str);
		if (isset($user->error)) {
			echo '<h3>error:</h3>' . $user->error;
			echo '<h3>msg  :</h3>' . $user->error_description;
			exit;
		}

		$openid = $user->openid;
		$mod = Model::getInstance('Account', 'member');
		$ret = $mod->extlogin(DataAccount::PARTNER_QQ, $openid);
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
