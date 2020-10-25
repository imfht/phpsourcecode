<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/7/29
 * Time: 上午8:42
 */

namespace Partini\Cache;


class Redis implements CacheInterface
{

    protected $options;
    protected $handler;

    public function __construct($config)
    {
        if(!extension_loaded('redis')){
            throw new CacheException('redis extension not loaded');
        }
        $options = array(
            'host' => $config->get('REDIS_HOST'),
            'port' => $config->get('REDIS_PORT'),
            'timeout' => $config->get('REDIS_TIMEOUT'),
            'prefix' => $config->get('REDIS_PREFIX')
        );
        $this->options = $options;
        $this->handler = new \Redis();
        $this->options['timeout'] > 0 ? $this->handler->connect($options['host'],$options['port'],$options['timeout']) : $this->handler->connect($options['host'],$options['port']);
    }

    public function get($key,$type_return = self::TYPE_ARRAY){
        $v = $this->handler->get($this->options['prefix'].$key);
        switch($type_return){
            case self::TYPE_ARRAY:
                $v_formatted = json_decode($v,true);
                break;
            case self::TYPE_OBJECT:
                $v_formatted = json_decode($v);
                break;
        }
        return is_null($v_formatted) ? $v : $v_formatted;
    }

    public function set($key,$value,$expire = null){
        $key = $this->options['prefix'].$key;
        $value = (is_object($value) || is_array($value)) ? json_encode($value) : $value;
        is_int($expire) ? $r = $this->handler->setex($key,$expire,$value) : $r = $this->handler->set($key,$value);
        return $r;
    }

    public function delete($key){
        $this->handler->delete($this->options['prefix'].$key);
    }

    public function flush(){
        $this->handler->flushDB();
    }
}