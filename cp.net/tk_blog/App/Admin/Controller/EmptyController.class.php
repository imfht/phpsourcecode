<?php
/**
 * 空控制器
 */
namespace Admin\Controller;
class EmptyController extends AdminBaseController {
	public function index(){
		redirect('/Admin');
	}
}