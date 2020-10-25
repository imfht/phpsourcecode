<?php
namespace Home\Controller;
use Think\Controller;

//404显示
class EmptyController extends Controller {

	//访问不存在的控制器时
	public function index() {
		header("HTTP/1.1 404 Not Found");  
		header("Status: 404 Not Found");  
		include(COMMON_PATH.'View/404.html');
		exit();
	}

	//访问不存在的方法时
	function _empty($name) {
		header("HTTP/1.1 404 Not Found");  
		header("Status: 404 Not Found");
		include(COMMON_PATH.'View/404.html');
		exit();
	}

}