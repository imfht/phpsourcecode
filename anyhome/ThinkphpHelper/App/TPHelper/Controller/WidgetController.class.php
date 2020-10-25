<?php
namespace TPHelper\Controller;
use Think\Controller;
class WidgetController extends CommonController {
	public function index()
	{
		$this->display();
	}

	public function preview($t = '',$tpl = '')
	{
		$tpl = "Widget/$t/".$tpl.'.html';
		$this->display($tpl);
	}
}