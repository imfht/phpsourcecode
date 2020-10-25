<?php
/**
 * 管理相关
 * 
 * @author ShuangYa
 * @package Blog
 * @category Library
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

namespace blog\model;
use \sy\base\Controller;
use \sy\base\i18n;
use \sy\base\Router;
use \sy\lib\Html;
use \sy\lib\db\Mysql;
use \sy\lib\Cookie;
use \sy\lib\Security;
use \blog\libs\Common;

class Admin {
	protected static $password = NULL;
	public static function init() {
		if (self::$password === NULL) {
			//强制不缓存
			header('expires: ' . date('d,d m y h:i:s', mktime(0, 0, 0, 1, 1, 2000)) . ' gmt');
			header('last-modified:' . gmdate('d,d m y h:i:s') . ' gmt');
			header('cache-control: private, no-cache,must-revalidate');
			header('pragma: no-cache');
			self::$password = Common::option('password');
		}
	}
	public static function checkPassword($password) {
		self::init();
		return Security::password($password) === self::$password;
	}
	public static function setLogin() {
		self::init();
		Cookie::set(['name' => 'auth', 'path' => '/', 'value' => md5(self::$password), 'httponly' => TRUE]);
	}
	public static function setLoginout() {
		self::init();
		Cookie::set(['name' => 'auth', 'path' => '/', 'value' => 'v', 'expire' => -1, 'httponly' => TRUE]);
	}
	public static function checkLogin() {
		self::init();
		if (isset($_REQUEST['auth'])) {
			$auth = $_REQUEST['auth'];
		} else {
			$auth = Cookie::get('auth');
		}
		if ($auth !== md5(self::$password)) {
			return FALSE;
		} else {
			if (!isset($_REQUEST['auth'])) {
				Cookie::set(['name' => 'auth', 'path' => '/', 'value' => $auth, 'httponly' => TRUE]);
			}
			return TRUE;
		}
	}
	public static function gotoLogin() {
		header('Location: ' . Router::createUrl('admin/page/login'));
		exit;
	}
	public static function gotoHome() {
		header('Location: ' . Router::createUrl('admin/page/home'));
		exit;
	}
}