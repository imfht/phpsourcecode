<?php
    if (!class_exists('Redis')){
		exit("未安装Redis扩展！");
	}
    //自动类加载 
    spl_autoload_register('classLoader');
    
    function classLoader($className)
    {
        require str_replace('_', '/', $className) . '.php';
    }
    
    
    
    $cache = new Cache_RedisString('name',3600);
    if ($cache->get() && $cache->cache_value)
    {//存在、直接取值
        echo $cache->cache_value;
    }
    else
    {//不存在则设置
        $cache->cache_value = "my name is redis for php demo";
        $cache->set();
		echo $cache->cache_value;
    }
?>