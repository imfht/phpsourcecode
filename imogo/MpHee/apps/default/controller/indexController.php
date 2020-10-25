<?php
class indexController extends baseController{

	protected function init(){
		$installDir = BASE_PATH . 'apps/install/';
		$lockFile = $installDir . 'install.lock';
		if( file_exists($installDir) && !file_exists($lockFile ) ){
			$this->redirect( url('install/index/index') );
		}
	}
	
	public function index(){
		$this->display();
	}
	
}