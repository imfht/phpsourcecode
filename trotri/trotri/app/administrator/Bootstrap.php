<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

use tfc\ap;
use tfc\mvc\Mvc;
use tfc\util\String;
use users\services\Account;

/**
 * Bootstrap class file
 * 程序引导类，在项目入口处执行，会依次执行类中以_init开头的方法，初始化项目参数
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Bootstrap.php 1 2013-04-11 12:06:30Z huan.song $
 * @package administrator
 * @since 1.0
 */
class Bootstrap extends ap\Bootstrap
{
	/**
	 * 初始化$_GET、$_POST、$_COOKIE值，XSSClean
	 * @return void
	 */
	public function _initRGPC()
	{
		$rawKeys = array('http_referer', 'content', 'show_code', 'stat_code', 'powerby', 'jump_url', 'menu_url', 'advert_url', 'html_style', 'html_script', 'html_head', 'html_body');

		foreach ($_GET as $key => $value) {
			if (in_array($key, $rawKeys)) {
				continue;
			}

			$_GET[$key] = String::specialchars_decode($value);
		}

		foreach ($_POST as $key => $value) {
			if (in_array($key, $rawKeys)) {
				continue;
			}

			$_POST[$key] = String::specialchars_decode($value);
		}

		foreach ($_COOKIE as $key => $value) {
			if (in_array($key, $rawKeys)) {
				continue;
			}

			$_COOKIE[$key] = String::specialchars_decode($value);
		}
	}

	/**
	 * 初始化默认的module、controller和action名
	 * @return void
	 */
	public function _initDefaultRouter()
	{
		$router = Mvc::getRouter();
		$router->setDefaultModule('system')
			   ->setDefaultController('site')
			   ->setDefaultAction('index');
	}

	/**
	 * 初始化用户账户信息
	 * @return void
	 */
	public function _initAccount()
	{
		$account = new Account();
		$account->initIdentity();
	}

	/**
	 * 初始化项目编码
	 * @return void
	 */
	public function _initEncoding()
	{
	}

	/**
	 * 初始化项目语言种类
	 * @return void
	 */
	public function _initLanguageType()
	{
	}

    /**
     * 初始化缓存
     * @return void
     */
    public function _initCache()
    {
    }

    /**
     * 初始化模板解析类
     * @return void
     */
    public function _initView()
    {
    }

    /**
     * 初始化路由规则
     * @return void
     */
    public function _initRoutes()
    {
    }
}
