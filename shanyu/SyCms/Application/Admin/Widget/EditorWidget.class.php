<?php
namespace Admin\Widget;
use Think\Controller;

class EditorWidget extends Controller {
	public function editArea($name='',$value=''){
    	$this->assign('name',$name);
		$this->assign('value',$value);
    	$this->display(MODULE_PATH.'Widget/Tpl/Editor/editArea.html');
	}
	public function mirror($name='',$value=''){
    	$this->assign('name',$name);
		$this->assign('value',$value);
    	$this->display(MODULE_PATH.'Widget/Tpl/Editor/mirror.html');
	}
}