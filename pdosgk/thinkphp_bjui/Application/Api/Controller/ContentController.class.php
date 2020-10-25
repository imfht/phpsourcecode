<?php
namespace Api\Controller;
use Think\Controller;
/**
 * 内容接口
 */
class ContentController extends Controller {

	public function add(){
		echo I('get.accessToken');
		echo 221;
	}

	public function edit(){
		echo edit;
	}
}