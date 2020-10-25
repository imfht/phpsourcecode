# 缓存

这一章，将从简单的缓存、再到高速缓存、最后延伸到多级缓存，逐步进行说明。  

## 简单本地缓存
这里所指的简单缓存，主要是存储在单台服务器上的缓存，例如使用系统文件的文件缓存，PHP语言提供的APCU缓存。因为实现简单，且部署方便。但其缺点也是明显的，如文件I/O读写导致性能低，不能支持分布式。所以在没有集群服务器下是适用的。  
  
### 文件缓存

例如，当需要使用文件缓存时，先在DI容器中注册对文件缓存到```\PhalApi\DI()->cache```。  

```php
$di->cache = new PhalApi\Cache\FileCache(array('path' => API_ROOT . '/runtime', 'prefix' => 'demo'));
```
初始化文件缓存时，需要传入配置数组，其中path为缓存数据的目录，可选的前缀prefix，用于区别不同的项目。  

然后便可在适当的场景使用缓存。  
```php
// 设置
PhalApi\DI()->cache->set('thisYear', 2015, 600);

// 获取，输出：2015
echo PhalApi\DI()->cache->get('thisYear');

// 删除
PhalApi\DI()->cache->delete('thisYear');
```

可以看到，在指定的缓存目录下会有类似以下这样的缓存文件。  
```bash
$ tree ./runtime/cache/
./runtime/cache/
└── 483
    └── 11303fe8f96da746aa296d1b0c11d243.dat
```

默认情况下，文件缓存会把缓存的key格式成缓存的文件名。如果需要保留原来的key作为缓存的文件名，可以在构建时把：enable_file_name_format设置为FALSE，则可以关闭格式化文件名。例如：
```php
$di->cache = new PhalApi\Cache\FileCache(array('path' => API_ROOT . '/runtime', 'prefix' => 'demo', 'enable_file_name_format' => FALSE));
```

再次进行缓存相关操作，保存的缓存文件将会变成：
```bash
$ tree ./runtime/cache/
./runtime/cache/
└── 483
    └── demothisYear
```

可方便通过缓存key直接对比查看缓存文件。

> 温馨提示：enable_file_name_format需要PhalApi 2.7.0及以上版本支持。

### APCU缓存

安装好APCU扩展和设置相关配置并重启PHP后，便可开始使用APCU缓存。APCU缓存的初始化比较简单，只需要简单创建实例即可，不需要任何配置。  
```php
$di)->cache = new PhalApi\Cache\APCUCache();
```
其他使用参考缓存接口，这里不再赘述。  

## 高速集群缓存

这里的高速集群缓存，是指备分布式存储能力，并且进驻内存的缓存机制。高速集群缓存性能优于简单缓存，并且能够存储的缓存容量更大，通常配置在其他服务器，即与应用服务器分开部署。其缺点是需要安装相应的PHP扩展，另外部署缓存服务，例如常见的Memcached、Redis。若需要考虑缓存落地，还要进一步配置。    
  
### Memcache/Memcached缓存
若需要使用Memcache/Memcached缓存，则需要安装相应的PHP扩展。PHP 7中已经逐渐不支持Memcache，因此建议尽量使用Memcached扩展。  

如使用Memcached：  
```php
$di->cache = new PhalApi\Cache\MemcachedCache(array('host' => '127.0.0.1', 'port' => 11211, 'prefix' => 'demo_'));
```
初始化Memcached时，需要传递一个配置数组，其中host为缓存服务器，port为缓存端口，prefix为可选的前缀，用于区别不同的项目。配置前缀，可以防止同一台MC服务器同一端口下key名冲突。对于缓存的配置，更好的建议是使用配置文件来统一管理配置。例如调整成：  
```php
$di->cache = new PhalApi\Cache\MemcachedCache(DI()->config->get('sys.mc'));
```
相应的配置，则在./config/sys.php中的mc选项中统一维护。  

完成了Memcached的初始化和注册后，便可考缓存接口进行使用，这里不再赘述。Memcache的初始化和配置和Memcached一样。    

### 如何配置多个Memcache/Memcached实例？ 
实际项目开发中，当需要连接多个Memcache/Memcached实例，可以在单个实例配置基础上采用以下配置：  
```php
$config = array(
    'host'    => '192.168.1.1, 192.168.1.2',  //多个用英文逗号分割
    'port'    => '11211, 11212',              //多个用英文逗号分割
    'weight'  => '20, 80',                    //（可选）多个用英文逗号分割
);

$di->cache = new PhalApi\Cache\MemcachedCache($config);
```
上面配置了两个MC实例，分别是：

 + 192.168.1.1，端口为11211，权重为20
 + 192.168.1.2，端口为11212，权重为80    
  
其中，权重是可选的。并且**以host域名的数量为基准**，即最终MC实例数量以host的个数为准。端口数量不足时取默认值11211，多出的端口会被忽略；同样，权重数量不足时取默认值0，多出的权重会被忽略。  
  
如下，是一份稀疏配置：  
```php
$config = array(
    'host'    => '192.168.1.1, 192.168.1.2, 192.168.1.3',
    'port'    => '11210',
);
```
相当于：  

 + 192.168.1.1，端口为11210，权重为0（默认值）  
 + 192.168.1.2，端口为11211（默认值），权重为0（默认值）  
 + 192.168.1.3，端口为11211（默认值），权重为0（默认值）    
  
请注意，通常不建议在权重weight使用稀疏配置，即要么全部不配置权重，要么全部配置权重，以免部分使用默认权重为0的MC实例不生效。  

### Redis缓存
当需要使用Redis缓存时，需要先安装对应的Redis扩展。  

简单的Redis缓存的初始化如下：  
```php
$config = array('host' => '127.0.0.1', 'port' => 6379);
$di->cache = new PhalApi\Cache\RedisCache($config);
```

关于Redis的配置，更多选项如下。  

Redis配置项|是否必须|默认值|说明
---|---|---|---
type|否|unix|当为unix时使用socket连接，否则使用http连接
socket|type为unix时必须|无|unix连接方式
host|type不为unix时必须|无|Redis域名
port|type不为unix时必须|6379|Redis端口
timeout|否|300|连接超时时间，单位秒
prefix|否|phalapi:|key前缀
auth|否|空|Redis身份验证
db|否|0|Redis库


## 扩展：添加新的缓存实现

当需要实现其他缓存机制时，例如使用COOKIE、SESSION、数据库等其他方式的缓存，可以先实现具体的缓存类，再重新注册```\PhalApi\DI()->cache```即可。  

首先，简单了解下PhalApi中的缓存接口[PhalApi\Cache](https://github.com/phalapi/kernal/blob/master/src/Cache.php)。  
```php
<?php
namespace PhalApi;

/**
 * PhalApi\Cache 缓存接口
 *
 * @package     PhalApi\Cache
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-02-04
 */

interface Cache {

	/**
	 * 设置缓存
	 * 
	 * @param string $key 缓存key
	 * @param mixed $value 缓存的内容
	 * @param int $expire 缓存有效时间，单位秒，非时间戳
	 */
    public function set($key, $value, $expire = 600);

    /**
     * 读取缓存
     * 
     * @param string $key 缓存key
     * @return mixed 失败情况下返回NULL
     */
    public function get($key);

    /**
     * 删除缓存
     * 
     * @param string $key
     */
    public function delete($key);
}

```
此```PhalApi\Cache```缓存接口，主要有三个操作：设置缓存、获取缓存、删除缓存。设置时，缓存不存在时添加，缓存存在时则更新，过期时间单位为秒。当获取失败时，约定返回NULL。  

所以，新的缓存实现类应按规约层的接口签名完成此缓存接口的实现。  
