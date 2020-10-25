<?php
namespace app\index\controller;
use app\common\controller\HomeBase;

class Group extends  HomeBase
{
	public function _initialize()
	{
		parent::_initialize();
	}
	
   public function index(){
   	
   
   	return $this->fetch();
   	
   }
}
