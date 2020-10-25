<?php

namespace SgIoc\Cache;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/9
 * Time: 14:07
 */
class MemcacheGc extends \Memcache
{
    private $server_id;

    public function __construct($host, $port)
    {
        $this->server_id = "$host:$port";
        $this->connect($host, $port);
    }

    // 回收所有过期的内存
    public function gc()
    {
        $t     = time();
        $_this = $this;
        $func  = function ($key, $info) use ($t, $_this) {
            if ($info[1] - $t < -30)   //30秒过期的缓冲
            {
                $_this->delete($key);
            }
        };
        $this->lists($func);
    }

    // 查看所有缓存内容的信息
    public function info()
    {
        $t    = time();
        $func = function ($key, $info) use ($t) {
            echo $key, ' => Exp:', $info[1] - $t, 's<br/>'; //查看缓存对象的剩余过期时间
        };
        $this->lists($func);
    }

    private function lists($func)
    {
        $sid   = $this->server_id;
        $items = $this->getExtendedStats('items');  //获取memcached状态
        foreach ($items[$sid]['items'] as $slab_id => $slab)  //获取指定server id 的 所有Slab
        {
            $item = $this->getExtendedStats('cachedump', $slab_id, 0);        //遍历所有Slab
            foreach ($item[$sid] as $key => $info)  //获取Slab中缓存对象信息
            {
                $func($key, $info);
            }
        }
    }
}