<?php
namespace Admin\Controller;

class HooksController extends CommonController {


	public function _before_add(){
		
		$this->assign('type',C('HOOKS_TYPE'));
	}
	public function _before_edit(){
		
		$this->assign('type',C('HOOKS_TYPE'));
	}
	
public function _after_list($list){
	
	//对type进行处理
	 return int_to_string($list, array('type'=>C('HOOKS_TYPE')));

}




}
