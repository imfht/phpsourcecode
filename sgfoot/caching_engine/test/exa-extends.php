<?php
/**
 * 扩展缓存
 * User: Administrator
 * Date: 2018/3/27
 * Time: 17:28
 */
//第一步:复制MemcacheStore或FileStore文件,如 ApcStore
class ApcStore extends StoreAbstract
{

}
//第二步:注入服务
$config = array();
CacheContainer::bind('apc', function () use ($config) {
    return new ApcStore($config);
});

//或者,修改src/Cache.php里的文件,参考registerFile方法

//第三步:使用扩展缓存
Cache::store('apc')->put('key', 'abc');