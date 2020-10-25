<?php
class Controller{
	public $ControllerName;
	public $view;
	function __construct() {
		$this->view = new View();
	}
	public function render($tpl = '', $vars = []) {
		return $this->view->render($tpl, $vars);
	}
	public function layout(){
		$this->view->layout();
		return $this;
	}
	public static function assign($vars) {
		return $this->$view->assign($vars);
	}
	public static function set_tpl($n) {
		return $this->$view->set_tpl($n);
	}
	public static function set_resp_type($t) {
		return Resp::$RespType = $t;
	}
	public function redirect($url) {
		header("Location: $url");
	}
}