#PHP_Redis

**目录结构**

----Cache
------|-RedisCache.php
------|-RedisPool.php
------|-RedisString.php
----demo.php

**使用方法**

1. 引入Cache目录下的类，demo采用spl_autoload_register 加载

```
    //自动类加载 
    spl_autoload_register('classLoader');
    function classLoader($className)
    {
        require str_replace('_', '/', $className) . '.php';
    }
```

2.. 实例化对于的类型类、调用对应的方法

   - **String** get()/set()/delete()

   ```
    $cache = new Cache_RedisString('name',3600);
    if ($cache->get())
    {//存在、直接取值
        echo $cache->cache_value;
    }
    else
    {//不存在则设置
        $cache->cache_value = "my name is redis for php demo";
        $cache->set();
    }
   ```