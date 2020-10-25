<?php

namespace Api\Controller;

use Think\Controller;

class TestController extends Controller {

	public function index() {
		$wx = new \Api\Controller\WxController();
		header("content-type:text/html; charset=utf-8");
		echo '<pre>';
		dump($wx->test());
		echo '</pre>';
	}

}
