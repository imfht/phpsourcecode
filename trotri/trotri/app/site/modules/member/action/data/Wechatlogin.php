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
use tfc\saf\Cfg;
use tfc\saf\Cookie;
use system\services\Options;

/**
 * Wechatlogin class file
 * 第三方账号登录：微信联登，必须在微信客户端才有用
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Wechatlogin.php 1 2014-01-18 14:19:29Z huan.song $
 * @package modules.member.action.data
 * @since 1.0
 */
class Wechatlogin extends DataAction
{
	/**
	 * (non-PHPdoc)
	 * @see \tfc\mvc\interfaces\Action::run()
	 */
	public function run()
	{
		$cookie = new Cookie('cookie');

		$httpReferer = Ap::getRequest()->getTrim('http_referer');
		if ($httpReferer === '') {
			$httpReferer = 'index.php';
		}

		HttpCookie::add('http_referer', $httpReferer);

		$appid    = Cfg::getApp('appid', 'wechat', 'extlogin');
		$callback = Options::getSiteUrl() . '/index.php?r=member/data/wechatcallback';
		$scope    = 'snsapi_base';
		$state    = md5(uniqid(rand(), TRUE)); //CSRF protection
		$cookie->add('state', $state);

		$loginUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?response_type=code'
				  . '&appid='        . $appid
				  . '&redirect_uri=' . urlencode($callback)
				  . '&state='        . $state
				  . '&scope='        . $scope
				  . '#wechat_redirect';

		Ap::getResponse()->location($loginUrl);
	}
}
