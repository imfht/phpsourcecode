<?php
namespace Admin\Widget;
use Think\Controller;

class ChosenWidget extends Controller {
	public function one($name='',$value='',$select=array()){

		$this->assign('name',$name);
		$this->assign('value',$value);
		$this->assign('select',$select);
		
		$this->display(MODULE_PATH.'Widget/Tpl/Chosen/one.html');
	}

	public function many($name='',$value='',$select=array()){

		$this->assign('name',$name);
		$this->assign('value',$value);
		$this->assign('select',$select);
		
		$this->display(MODULE_PATH.'Widget/Tpl/Chosen/many.html');
	}

}
