<?php

if(!defined('__ROOT__')){
    define('__ROOT__', dirname(dirname(__FILE__)));
}
require_once(__ROOT__.'/inc/webapp.class.php');

class News extends WebApp{
	
    function __construct($args=null){
		        
		if($_SESSION['language'] && $_SESSION['language'] == "en_US"){
			$this->setTbl(GConf::get('tblpre').'news');
		}
		else{
			$this->setTbl(GConf::get('tblpre').'news');
		}
		
		parent::__construct();
		
    }
}
?>
