<?php
/* DB Connection config, for all db settings.
 * v0.1
 * wadelau@ufqi.com
 * since Wed Jul 13 18:20:28 UTC 2011
 */

if(!defined('__ROOT__')){
  define('__ROOT__', dirname(dirname(__FILE__)));
}

require_once(__ROOT__."/inc/config.class.php");

# db master
class Config_Master{
	var $mDbHost     = "";	
	var $mDbUser     = "";
	var $mDbPassword = ""; 
	var $mDbPort     = "";	
	var $mDbDatabase = "";
	var $mDbSock = '';
	var $mDbPersistent = true;
	
	function __construct(){
		$gconf = new GConf();
		$this->mDbHost = $gconf->get('dbhost');
		$this->mDbPort = $gconf->get('dbport');
		$this->mDbUser = $gconf->get('dbuser');
		$this->mDbPassword = $gconf->get('dbpassword');
		$this->mDbDatabase = $gconf->get('dbname');
		$this->mDbSock = $gconf->get('dbsock');
		if(isset($gconf->get('dbpersistent'))){
			$this->mDbPersistent = $gconf->get('dbpersistent');
		}
	} 
}

# db slave
class Config_Slave{
	var $mDbHost     = "";	
	var $mDbUser     = "";
	var $mDbPassword = ""; 
	var $mDbPort     = "";	
	var $mDbDatabase = "";
	var $mDbSock = '';
	
	function __construct(){
		$gconf = new GConf();
		$this->mDbHost = $gconf->get('dbhost_slave');
		$this->mDbPort = $gconf->get('dbport_slave');
		$this->mDbUser = $gconf->get('dbuser_slave');
		$this->mDbPassword = $gconf->get('dbpassword_slave');
		$this->mDbDatabase = $gconf->get('dbname_slave');
		$this->mDbSock = $gconf->get('dbsock');
		if(isset($gconf->get('dbpersistent'))){
			$this->mDbPersistent = $gconf->get('dbpersistent');
		}
	} 
}

# cache master
class Cache_Master{
	
	var $chost = '';
	var $cport = '';
	var $expireTime = 1800; # 30 * 60; 
	
	function __construct(){
		$this->chost = GConf::get('cachehost');
		$this->cport = GConf::get('cacheport');
		$this->expireTime = GConf::get('cacheexpire');
		
	}
	
}

# file sys
class File_System{
	var $uplddir = '';
	var $reuse = false;
	
	function __construct(){
		$this->uplddir = GConf::get('upld');
		$this->reuse = GConf::get('enable_filehandle_share');
	}
	
}

//-- Todo

# connection pool ?

?>
