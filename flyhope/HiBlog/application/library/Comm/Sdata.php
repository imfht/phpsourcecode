<?php
/**
 * 进程数据缓存
 *
 * @package Comm
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Comm;
abstract class Sdata {
    
    /**
     * 缓存的数据
     * 
     * @var array
     */
    static protected $_data = array();
    
    /**
     * 最多缓存数据个数
     * 
     * @var int
     */
    static $max_count = 500;
    
    /**
     * 写入一个数值 
     * 
     * @param string $key   KEY名
     * @param mixed  $value VALUE名
     * 
     * @return \void
     */
    static public function set($key, $value) {
        if(count(self::$_data) > self::$max_count) {
            array_shift(self::$_data);
        }
        self::$_data[$key] = $value;
    }
    
    /**
     * 获取一个数值
     * 
     * @return \void
     */
    static public function get($key) {
        return isset(self::$_data[$key]) ? self::$_data[$key] : null;
    }
    
    /**
     * 获取全部数据
     * 
     * @return \array
     */
    static public function data() {
        return self::$_data;
    }
    
    /**
     * 清除所有数据
     * 
     * @return \void
     */
    static public function clear() {
        self::$_data = array();
    }
    
    /**
     * 获取一个数值，如果不存在则执行回调后写入返回
     * 
     * @param string   $key      键
     * @param callable $callback 回调方法
     * 
     * @return \void
     */
    static public function getValue($key, callable $callback) {
        $data = self::get($key);
        if($data === null) {
            $data = call_user_func($callback);
            if($data !== null) {
                self::set($key, $data);
            }
        }
        return $data;
    }
    
}
