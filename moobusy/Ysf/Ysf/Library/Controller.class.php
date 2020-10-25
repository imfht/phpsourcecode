<?php
namespace Ysf;

/**
 * Controller class
 */
class Controller
{
	public $assign = [];
	
	public function assign($data)
	{
		if(is_array($data)){
			$this->assign = array_merge($this->assign,$data);
		}else{
			$this->assign[$data] = func_get_args()[1];
		}
		return $this;
	}	
	
	public function view($template='')
	{
		$template = APP_PATH . '/view/'.CONTROLLER_NAME.'/'.(!empty($template)?$template:ACTION_NAME).'.html';
		exit(View::display($template,$this->assign));
	}
	
	public function render($template='')
	{
		$template = APP_PATH . '/view/'.CONTROLLER_NAME.'/'.(!empty($template)?$template:ACTION_NAME).'.html';
		return View::display($template,$this->assign);
	}
}