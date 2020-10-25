<?php
/**
 * 空控制器
 */
namespace Home\Controller;
use Common\Controller\HomebaseController;
class EmptyController extends HomebaseController {
	public function index(){
		redirect('/');
	}
}