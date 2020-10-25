<?php

/**
 * 跳转
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

class Go extends Controller {
	public function __construct() {
	}
	public function actionHelp() {
		$url = require(Sy::$appDir . 'data/goto/help.php');
		$q = $_GET['q'];
		if (isset($url[$q])) {
			header('Location: ' . $url[$q]);
		}
	}
}