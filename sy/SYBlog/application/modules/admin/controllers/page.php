<?php

/**
 * 管理后台
 * 
 * @author ShuangYa
 * @package Blog
 * @category Controller
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

namespace blog\controller;
use \Sy;
use \sy\base\Controller;
use \sy\base\Router;
use \sy\lib\Security;
use \blog\model\Admin;

class Page extends Controller {
	/**
	 * Home
	 */
	public function actionHome() {
		if (!Admin::checkLogin()) {
			Admin::gotoLogin();
		}
		Sy::setMimeType('html');
		$this->assign('page', 'home');
		$this->display('page/home');
	}
	/**
	 * 登录
	 */
	public function actionLogin() {
		if (Admin::checkLogin()) {
			Admin::gotoHome();
		}
		Sy::setMimeType('html');
		if (isset($_POST['password'])) {
			if (Admin::checkPassword($_POST['password'])) {
				Admin::setLogin();
				echo 1;
			} else {
				echo 0;
			}
		} else {
			if (!empty($_SERVER['HTTP_REFERER'])) {
				$redirect = $_SERVER['HTTP_REFERER'];
			} else {
				$redirect = Router::createUrl('admin/page/home');
			}
			$redirect = addslashes($redirect);
			$this->assign('redirect', $recirect);
			$this->display('page/login');
		}
	}
	/**
	 * 退出
	 */
	public function actionLoginout() {
		Admin::setLoginout();
		header('Location: ' . Router::createUrl('admin/page/login'));
	}
}
