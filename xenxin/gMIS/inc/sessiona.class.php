<?php
/* Session admin, handling users sessions in all apps
 * v0.1
 * wadelau@ufqi.com
 * Sat Jul 23 09:50:58 UTC 2011
 * Mon, 6 Mar 2017 21:59:03 +0800, implementing
 */

if(!defined('__ROOT__')){
  define('__ROOT__', dirname(dirname(__FILE__)));
}

require_once(__ROOT__."/inc/config.class.php");
require_once(__ROOT__."/inc/socket.class.php");
require_once(__ROOT__."/inc/session.class.php");
#require_once(__ROOT__."/inc/class.connectionpool.php");

class SessionA {

	var $conf = null; 
	var $sessionconn = null;
	
 	//- construct
	function __construct($sessionConf = null){
		
		$sessionConf = ($sessionConf==null ? 'Session_Master' : $sessionConf);
		$this->conf = new $sessionConf;
		$sessionDriver = GConf::get('sessiondriver');
		$this->sessionconn = new $sessionDriver($this->conf);
		
	}
	
	//-
	function __destruct(){
		$this->close();
	}

	# get
	public function get($k){
		# @todo
	    $k = $this->_md5k($k);
		return $this->sessionconn->get($k);
				
	}
	
	# set
	public function set($k, $v){
	    # @todo
	    $k = $this->_md5k($k);
		$rtn = $this->sessionconn->set($k, $v);
		return $rtn;
		
	}
	
	# delete
	public function rm($k){
		$k = $this->_md5k($k);
		$rtn = $this->sessionconn->del($k);
		return $rtn;
	}
	
	# shorten key
	private function _md5k($k){
		return strlen($k)>32 ? md5($k) : $k;
	}
 	
	//-
	function close(){
	    # @todo, long conn?
	    # need sub class to override with actual close handler
	    $this->sessionconn->close();
	    return true;
	}
	
 }
?>
