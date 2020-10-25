<?php
/**
 * Memcache基类
 * User: chenxiong<cxmvc@qq.com>
 * Date: 2013-09-15
 */
namespace nb\utility;

use nb\Config;
use nb\Pool;

class Memcache extends \Memcache {

 	function __construct($host,$port,$timeout=0,$connect=false){
 		$result = $connect?$this->pconnect($host,$port):$this->connect($host,$port);
 		if (!$result) {
 			throw new \Exception("cannot connect to Memcache server {$host}:{$port}");
 		}
 	}

 	/**
     * 获取一个Memcache对象
 	 * @param string|array $server
 	 * @return Memcache
 	 */
	public static function instance($server = 'memcache'){
		if(is_string($server)) {
			$server = conf($server);
			$server or $server = Config::getx('memcache');
		}
	    $key = $server['host'].$server['host'];
        if($obj = Pool::get($key)) {
            return $obj;
        }
        $obj = get_called_class()?:'nb\\utility\\Memcache';
        return Pool::object($key,$obj,[
            $server['host'],$server['port'],$server['connect']
        ]);
	}

 }
 