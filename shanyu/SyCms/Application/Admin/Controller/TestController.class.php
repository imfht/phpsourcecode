<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class TestController extends AdminBaseController {
	public function index(){
		// $test=array('lili'=>'gaozhong','jim'=>'chuzhong');
		// F('Test',$test);

		// \Lib\File::cache('Tests',$test);
		$test_file=F('Tests');
		dump($test_file);
	}

	public function index2(){
		$result=D('Table')->getCreateSql('admin_log');
		dump($result);
	}

	public function bList(){

	}

	public function bForm(){
		if(IS_POST){
			print_r($_POST);exit;
		}
		$this->display();
	}


}