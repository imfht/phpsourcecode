<?php
/* DB Connection config, for all db settings.
 * v0.1
 * wadelau@ufqi.com
 * since Wed Jul 13 18:20:28 UTC 2011
 */

class Config_Master{
	var $mDbHost     = "";	
	var $mDbUser     = "";
	var $mDbPassword = ""; 
	var $mDbPort     = "";	
	var $mDbDatabase = "";
	var $mDbPersistent = true;
	
	function __construct(){
		$gconf = new Gconf();
		$this->mDbHost = $gconf->get('dbhost');
		$this->mDbPort = $gconf->get('dbport');
		$this->mDbUser = $gconf->get('dbuser');
		$this->mDbPassword = $gconf->get('dbpassword');
		$this->mDbDatabase = $gconf->get('dbname');
		if(null !== $gconf->get('dbpersistent')){
			$this->mDbPersistent = $gconf->get('dbpersistent');
		}
	} 
}

class Config_Stats{
	var $mDbHost     = "";	
	var $mDbUser     = "";
	var $mDbPassword = ""; 
	var $mDbPort     = "";	
	var $mDbDatabase = "";
	
	function __construct(){
		$gconf = new Gconf();
		$db_suffix = '_stat';
		$this->mDbHost = $gconf->get('dbhost'.$db_suffix);
		$this->mDbPort = $gconf->get('dbport'.$db_suffix);
		$this->mDbUser = $gconf->get('dbuser'.$db_suffix);
		$this->mDbPassword = $gconf->get('dbpassword'.$db_suffix);
		$this->mDbDatabase = $gconf->get('dbname'.$db_suffix);
		if(null !== $gconf->get('dbpersistent')){
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

# session service
class Session_Master{
    
    var $expireTime = 1800; # 30 * 60;

    function __construct(){
        
        $this->expireTime = GConf::get('sesssionexpire');

    }
}

?>
