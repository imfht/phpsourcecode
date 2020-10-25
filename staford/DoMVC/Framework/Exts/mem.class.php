<?php

class Mem
{
    private static $CONN = null; //连接资源

    /**
     * 构造函数，初始化到缓存服务器的链接
     * @param string $host 缓存服务器地址
     * @param string $port 访问端口
     * @param string $timeout 超时时间
     * @param boolean $pconnect 是否启用持久链接
     * @return void
     */
    public function __construct($host = '127.0.0.1', $port = '11211', $timeout = 1,
        $pconnect = false)
    {
        if (!class_exists('memcache')) {
            throw new Exception("Service can not support memcache", 1);
        }
        $memcache = new memcache;
        if ($pconnect) {
            if (!$memcache->pconnect($host, $port, $timeout)) {
                throw new Exception("Can not connect to the memcache service host", 1);
            }
        } else {
            if (!$memcache->connect($host, $port)) {
                throw new Exception("Can not connect to the memcache service host", 1);
            }
        }
        self::$CONN = $memcache;
    }

    /**
     * 取得目标数据
     * @param mixed $key 缓存名称
     * @return mixed
     */
    public function get($key)
    {
        return self::$CONN->get($key);
    }

    /**
     * 设置一个缓存，默认不过期
     */
    public function set($key, $val = null, $expire = 0, $isCompress = true)
    {
        if ($isCompress) {
            $isCompress = MEMCACHE_COMPRESSED;
        }
        return self::$CONN->set($key, $val, $isCompress, $expire);
    }

    /**
     * 替换一个缓存，默认不过期
     */
    public function replace($key, $val = null, $expire = 0, $isCompress = true)
    {
        if ($isCompress) {
            $isCompress = MEMCACHE_COMPRESSED;
        }
        return self::$CONN->replace($key, $val, $isCompress, $expire);
    }

    /**
     * 对一个缓存进行判断，如果不存在就设置新缓存，如果存在就替换其缓存内容
     */
    public function autoReplace($key, $val = null, $expire = 0, $isCompress = true)
    {
        if ($isCompress) {
            $isCompress = MEMCACHE_COMPRESSED;
        }
        if (self::$CONN->set($key, $val, $isCompress, $expire)) {
            return true;
        } else {
            return self::$CONN->replace($key, $val, $isCompress, $expire);
        }
    }

    /**
     * 删除一个缓存
     */
    public function del($key, $timeout = 0)
    {
        return self::$CONN->delete($key, $timeout);
    }

    /**
     * 清除所有缓存
     */
    public function clear()
    {
        return self::$CONN->flush();
    }
}

?>