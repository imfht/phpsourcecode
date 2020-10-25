<?php
class View{
	public $TplRoot;
	public $TplName;
	public $TplExt;
	public $Vars;
	function __construct() {
		//set the tpl root
		$themeName = Conf::get('Theme');
		$ext = Conf::get('TplExt');
		$this->TplExt = $ext;
		$this->TplRoot = empty($themeName)?TplRoot . '/':TplRoot . '/' . $themeName . '/';
		$this->TplName = App::$HandlerClassName . '/' . App::$HandlerFunction;
		$this->Layout = Conf::get('Layout');
		$this->layoutStr = "";
		$this->Vars = [];
		//$this->Vars = ['conf' => Conf::$conf, 'post' =>$_POST, 'get' => $_GET, 'cookie' => $_COOKIE, 'session' => $_SESSION];
	}
	public function layout() {
		$layoutPath = $this->TplRoot . $this->Layout . $this->TplExt;
		if (! file_exists($layoutPath)) {
			return false;
		}
		extract($this->Vars);
		$nightc = ['conf' => Conf::$conf, 'post' =>$_POST, 'get' => $_GET, 'cookie' => $_COOKIE, 'session' => $_SESSION];
		if ($this->Layout != false) {
			ob_start();
			@include($layoutPath);
			$this->layoutStr = ob_get_contents();
			@ob_end_clean();
		}
	}
	public function render($tpl = "", $vars = []) {
		//check the tpl
		if (is_string($tpl) && $tpl != "") {
			$tplArr = explode("/", $tpl);
			if (count($tplArr) == 2) {
				$this->TplName = $tpl;
			} else {
				$this->TplName = App::$HandlerClassName . '/' . $tpl;
			}
		}
		$tplFilePath = $this->TplRoot . $this->TplName . $this->TplExt;
		if (! file_exists($tplFilePath)) {
			return false;
		}
		if (is_array($vars)) {
			$this->Vars = array_merge($this->Vars, $vars);
		}
		extract($this->Vars);
		$nightc = ['conf' => Conf::$conf, 'post' =>$_POST, 'get' => $_GET, 'cookie' => $_COOKIE, 'session' => $_SESSION];
		
		ob_start();
		@include($tplFilePath);
		$buffer = ob_get_contents();
		@ob_end_clean();
		//layout
		if ($this->Layout != false && !empty($this->layoutStr)) {
			$buffer = str_replace('{{content}}', $buffer, $this->layoutStr);
		}
		return $buffer;
	}
	public function assign($vars) {
		if (is_array($vrs)) {
			$this->Vars = array_merge($this->Vars, $vars);
		}
	}
	public static function set_tpl($n) {
		$this->TplName = $tpl;
		$tplFilePath = $this->TplRoot . $this->TplName . $this->TplExt;
		//exit($tplFilePath);
		if (! file_exists($tplFilePath)) {
			return false;
		}
		return true;
	}
}