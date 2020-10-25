<?php
namespace Redis;

class Redis
{    
    private static $instance;
    
    public static function config(){
        return array(
            'url'=>'127.0.0.1',
            'port'=>'6379',
        );
    }
        
    public static function redis(){        
        return self::getInstance();
    }
        
    public static function getInstance(){
    
        if(!self::$instance){
            self::$instance = new \Redis();
            $config = self::config();
            self::$instance->connect($config['url'], $config['port']);            
        }
        return self::$instance;
    }
}