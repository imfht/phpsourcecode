<?php

class IndexAction extends GlobalAction {

	public function index(){
		
		$this -> os = PHP_OS;

		$this -> phpversion = phpversion();
		
		$this -> software = $_SERVER["SERVER_SOFTWARE"];

		$mysql_ver = M() -> query('SELECT VERSION();');
		
		if(is_array($mysql_ver)){
			
			$this -> mysql_ver = $mysql_ver[0]['VERSION()'];
			
		}else {
			
			$this -> mysql_ver = '';
			
		}
		
		$this -> articleNum = D('article') -> count();
		
		$this -> productNum = D('article') -> count();

	    $this -> charset = strtoupper(DOU_CHARSET);
	    
	    $this -> safe_mode = (boolean) ini_get('safe_mode') ? '是' : '否';
	    
	    $this -> safe_mode_gid = (boolean) ini_get('safe_mode_gid') ? '是' : '否';
	    
	    $this -> socket = function_exists('fsockopen') ? '是' : '否';
	    
	    $this -> timezone = function_exists("date_default_timezone_get") ? date_default_timezone_get() : '未知';
	    
	    $this -> gd = extension_loaded("gd") ? '是' : '否';

	    $this -> zlib = function_exists('gzclose') ? '是' : '否';
		
		$this -> environment_upload = ini_get('file_uploads') ? ini_get('upload_max_filesize') : '不支持';

		$this -> display();
	
	}
	
}