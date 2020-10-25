<?php
/**
 * Redis基类
 * User: chenxiong<cxmvc@qq.com>
 * Date: 2013-09-15
 */
namespace nb\utility;

use nb\Config;
use nb\Pool;

class Redis extends \Redis {

    /**
     * Redis的构造函数
     * 建议使用getInstance来获取Redis的对象
     */
    function __construct($host, $port, $db = 0,$pconnect=false) {
        $connect = $pconnect?$this->pconnect($host, $port):$this->connect($host, $port);
        if (!$connect) {
            throw new \Exception("cannot connect to Redis server {$host}:{$port}");
        }
        $this->select($db);
    }

    /**
     * 获取一个redis实例
     * @param string|array $server
     * @return Redis
     */
    public static function instance($server = 'redis') {
        if(is_string($server)) {
            $server = conf($server);
            $server or $server = Config::getx('redis');
        }
        $key = $server['host'] . $server['port'].$server['db'];
        if($obj = Pool::get($key)) {
            return $obj;
        }
        $pconnect = isset($server['connect'])?$server['connect']:false;
        $obj = get_called_class()?:'nb\\utility\\Redis';
        return Pool::object($key,$obj,[
            $server['host'],$server['port'],$server['db'],$pconnect
        ]);
    }


}
 