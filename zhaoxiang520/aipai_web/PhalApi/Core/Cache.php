<?php
/**
 * Cache.php
 * @since   2016-08-29
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace PhalApi\Core;


use PhalApi\Core\CacheLib\File;
use PhalApi\Core\CacheLib\Memcache;
use PhalApi\Core\CacheLib\Redis;
use PhalApi\Core\Exception\PAException;

class Cache {

    const CACHE_FILE      = 'File';
    const CACHE_REDIS     = 'Redis';
    const CACHE_MEMCACHE  = 'Memcache';

    protected static $instance = [];

    /**
     * 操作句柄
     * @var object
     * @access protected
     */
    protected static $handler;

    /**
     * 连接缓存
     * @param array         $options  配置数组
     * @param bool|string   $name 缓存连接标识 true 强制重新连接
     * @return mixed|null|object|File|Memcache|Redis
     * @throws PAException
     */
    public static function connect($options = [], $name = false) {
        $options = array_change_key_case($options, CASE_UPPER);
        $type = ucfirst(!empty($options['TYPE']) ? $options['TYPE'] : self::CACHE_REDIS);
        if (false === $name) {
            $name = $type;
        }

        if (true === $name || !isset(self::$instance[$name])) {
            $handle = null;
            switch ($type){
                case self::CACHE_REDIS:
                    $handle = new Redis( $options );
                    break;
                case self::CACHE_MEMCACHE:
                    $handle = new Memcache( $options );
                    break;
                case self::CACHE_FILE:
                    $handle = new File( $options );
                    break;
            }
            // 记录初始化信息
            if( !is_null($handle) ){
                if (true === $name) {
                    return $handle;
                } else {
                    self::$instance[$name] = $handle;
                }
            }else{
                throw new PAException('\\PhalApi\\Core\\CacheLib\\'.$type.T('L_CLASS.L_NOT_EXIST'));
            }
        }
        self::$handler = self::$instance[$name];
        return self::$handler;
    }

    /**
     * 自动初始化缓存
     * @access public
     * @return void
     */
    public static function init() {
        if (is_null(self::$handler)) {
            $options = [
                'DATA_CACHE_TYPE'        => Config::get('DATA_CACHE_TYPE'),
                'DATA_CACHE_HOST'        => Config::get('DATA_CACHE_HOST'),
                'DATA_CACHE_PORT'        => Config::get('DATA_CACHE_PORT'),
                'DATA_CACHE_PASSWORD'    => Config::get('DATA_CACHE_PASSWORD'),
                'DATA_CACHE_TIMEOUT'     => Config::get('DATA_CACHE_TIMEOUT'),
                'DATA_CACHE_EXPIRE'      => Config::get('DATA_CACHE_EXPIRE'),
                'DATA_CACHE_PERSISTENT'  => Config::get('DATA_CACHE_PERSISTENT'),
                'DATA_CACHE_PREFIX'      => Config::get('DATA_CACHE_PREFIX'),
            ];
            self::connect($options);
        }
    }

    /**
     * 判断缓存是否存在
     * @access public
     * @param string $name 缓存变量名
     * @return bool
     */
    public static function has($name) {
        self::init();
        return self::$handler->has($name);
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存标识
     * @param mixed  $default 默认值
     * @return mixed
     */
    public static function get($name, $default = false) {
        self::init();
        return self::$handler->get($name, $default);
    }

    /**
     * 写入缓存
     * @access public
     * @param string        $name 缓存标识
     * @param mixed         $value  存储数据
     * @param int|null      $expire  有效时间 0为永久
     * @return boolean
     */
    public static function set($name, $value, $expire = null) {
        self::init();
        return self::$handler->set($name, $value, $expire);
    }

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param string    $name 缓存变量名
     * @param int       $step 步长
     * @return false|int
     */
    public static function inc($name, $step = 1) {
        self::init();
        return self::$handler->inc($name, $step);
    }

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param string    $name 缓存变量名
     * @param int       $step 步长
     * @return false|int
     */
    public static function dec($name, $step = 1) {
        self::init();
        return self::$handler->dec($name, $step);
    }

    /**
     * 删除缓存
     * @access public
     * @param string    $name 缓存标识
     * @return boolean
     */
    public static function del($name) {
        self::init();
        return self::$handler->rm($name);
    }

    /**
     * 清除缓存
     * @access public
     * @return boolean
     */
    public static function clear() {
        self::init();
        return self::$handler->clear();
    }
}