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
use \blog\libs\Common;

class Index extends Controller {
	public function __construct() {
	}
	/**
	 * 首页
	 */
	public function actionIndex() {
		Sy::setMimeType('html');
		$this->assign('title', Common::option('sitename'));
		$this->display('index/index');
	}
}
