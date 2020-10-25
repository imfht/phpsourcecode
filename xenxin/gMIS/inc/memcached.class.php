<?php
/* memcache service, work with cachea.class
 * v0.1
 * wadelau@ufqi.com
 * Sat Jul 23 09:50:58 UTC 2011
 */

if(!defined('__ROOT__')){
  define('__ROOT__', dirname(dirname(__FILE__)));
}

require_once(__ROOT__."/inc/config.class.php");
require_once(__ROOT__."/inc/socket.class.php");
#require_once(__ROOT__."/inc/class.connectionpool.php");

class MEMCACHEDX {

	var $cport = '';
	var $chost = '';
	var $mcache = null;
	var $expireTime = 1800; # 30 * 60; # 30 minutes
	const persist_ConnId = 'gMIS_BUILD_IN_MC';
	const Default_Port = 11211; # Tue, 2 May 2017 19:04:43 +0800
	const Sock_Tag = '.sock';
	
 	//- construct
	function __construct($config=null){
		
		$this->cport = $config->cport;
		$this->chost = $config->chost;
		$this->expireTime = $config->expireTime;
		if($this->cport == 0 || $this->cport == ''){
		    $this->cport = self::Default_Port; # Memcached
		}
		if(class_exists('Memcached')){ # set true/false in production
			//- use built-in memcached functions
			$this->mcache = new Memcached(self::persist_ConnId);
			$this->mcache->setOption(Memcached::OPT_REMOVE_FAILED_SERVERS, true);
			$this->mcache->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
			$servers = $this->mcache->getServerList(); 
			$isConnected = 0;
			if(is_array($servers)) { 
				foreach ($servers as $server) {
					if(($server['host'] == $this->chost 
					        && $server['port'] == $this->cport)
						|| ($server['host'] == 'localhost')
					){
						$isConnected = 1;
						break;
					}
				}
			}
			if(!$isConnected){
				$this->mcache->addServer($this->chost, $this->cport);
			}
		}
		else{
			//- open socket, # @todo
			#$this->mcache 
			
		}
		
		#print __FILE__;
		#print_r($config);
		#var_dump($this->mcache);
		#print __FILE__;
		
		# others shared service may be relocated into its parent, webapp.class
		
	}
	
	//-
	function __destruct(){
		$this->close();
	}
	
	//- init
	private function _init(){
		if(!is_object($this->mcache)){
			$this->mcache = new Memcached(self::persist_ConnId);
			$this->mcache->setOption(Memcached::OPT_REMOVE_FAILED_SERVERS, true);
			$this->mcache->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
			$servers = $this->mcache->getServerList(); 
			if(is_array($servers)) { 
				foreach ($servers as $server) {
					if(($server['host'] == $this->chost 
					        && $server['port'] == $this->cport)
						|| ($server['host'] == 'localhost')
					){
						return $this->mcache;
					}
				}
			}
			$this->mcache->addServer($this->chost, $this->cport);
		}
		return $this->mcache;
	}
	
	//- set
	function set($k, $v, $expr){
		$rtn = '';
		
		if(!$this->mcache){ $this->_init(); }
		$rtn = $this->mcache->set($k, $v, ($expr>0?$expr:$this->expireTime));
		#debug(__FILE__."::set: k:$k, v:$v expire:[".$this->expireTime."] retn_code:[".$this->mcache->getResultCode()."] rtn:[$rtn]\n");
		
		return array($this->mcache->getResultCode(),$rtn);
		
	}
	
	//- get
	function get($k){
		if(!$this->mcache){ $this->_init(); }
		$rtn = $this->mcache->get($k);
		#debug(__FILE__."::get: k:$k, retn_code:[".$this->mcache->getResultCode()."] rtn-v:[$rtn]\n");
		return array($this->mcache->getResultCode(),$rtn);
	}
 	
	//- delete
	function del($k){
		
		if(!$this->mcache){ $this->_init(); }
		$rtn = $this->mcache->delete($k);
		
		return array($this->mcache->getResultCode(), $rtn);
		
	}
	
	//- close
	function close(){
	    if($this->mcache != null){
		  $this->mcache->quit();
	    }
	}
	
 }
?>
