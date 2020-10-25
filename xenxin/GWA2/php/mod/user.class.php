<?php
/* User class for user center
 * v0.1,
 * wadelau@ufqi.com,
 * Mon Jul 23 20:49:47 CST 2012
 */

if(!defined('__ROOT__')){
  define('__ROOT__', dirname(dirname(__FILE__)));
}
require_once(__ROOT__.'/inc/webapp.class.php');

class User extends WebApp{
	
	var $sep = "|";
	var $eml = "email";
	var $lang = null;
    
	//-
	function __construct($args=null){
		//-
		$this->setTbl(GConf::get('tblpre').'info_siteusertbl');
		
		# Parent constructors are not called implicitly
		#     if the child class defines a constructor.
		# In order to run a parent constructor, a call to parent::__construct()
		#     within the child constructor is required.
		parent::__construct();
		
		# lang
	    if(array_key_exists('lang', $args)){
			$this->lang = $args['lang'];   
			#debug("mod/pagenavi: lang:".serialize($this->lang)." welcome:".$this->lang->get('welcome'));
		}
		else{
			#debug("mod/pagenavi: lang: not config. try global?");
			global $lang;
			$this->lang = $lang; # via global?
		}
		
	}
	
	//-
	function setEmail($email){
		$this->set($this->eml,$email);
	}

	//-
	function getEmail(){
		return $this->get($this->eml);
	}

	//-
	function isLogin(){
		return $this->getId() != '';
	}

	function getUserName()
	{
		return $this->get('username');
	}

    function getGroup(){
        return $this->get('usergroup');
    }
	
}
?>
