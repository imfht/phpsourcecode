<?php
namespace app\admin\controller;

/**
* 后台主页控制器
*/
class Index extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
	}

	function index()
	{
		return view('index');
	}

	function home()
	{	
		return view('home');
	}

	function test(){
		$categorys = cache('categorys');
		var_dump($categorys);exit;
	}

	
}