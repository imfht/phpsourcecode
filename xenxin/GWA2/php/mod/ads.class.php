<?php
   /* Webcase class 
 * v0.1,
 * wadelau@ufqi.com,
 * 2012-7-27
 */

if(!defined('__ROOT__')){
		define('__ROOT__', dirname(dirname(__FILE__)));
}
require_once(__ROOT__.'/inc/webapp.class.php'); 

class ADS extends WebApp{

		var $adname = "";

		function __construct() {
			$this->dba = new DBA();
			
			if($_SESSION['language'] && $_SESSION['language'] == "en_US"){
				$this->setTbl(GConf::get('tblpre').'en_info_ads');
			}
			else{
				$this->setTbl(GConf::get('tblpre').'ch_info_ads');
			}
			
			parent::__construct();
			
		}
}
?>
