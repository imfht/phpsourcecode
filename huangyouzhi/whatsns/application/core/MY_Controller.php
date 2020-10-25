<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class MY_Controller extends CI_Controller {
	var $whitelist;
	function __construct() {
	
		parent::__construct ();

	
		
	
	}
	
}
class ADMIN_Controller extends MY_Controller{
	var $whitelist;
	function __construct() {
		
		parent::__construct ();
	
		if($this->user['groupid']!=1&&$this->user['groupid']!=2&&$this->user['groupid']!=3){
			$this->message("您无权限访问后台");
			exit();
		}
	}
	
	
}