####SimpleRedis
轻量级的的PHP redis客户端，纯净PHP实现，不需要安装任何扩展。PHPRedis是以扩展形式提供的，可能有一些局限性；Predis不需要扩展，但过于臃肿，可能很多应用用不到那么多的功能。那么，来试试SimpleRedis吧。
####使用
```php
<?php 
require('SimpleRedis.class.php');

//连接服务器
$redis = new SimpleRedis(array(
	'host' => '127.0.0.1',
	'port' => '6379',
	'password' => '123456' //有密码则提供
));

//存储字符串
$redis->set('foo','crazymus'); //返回值，true OR false
//取回字符串
$redis->get('foo'); //返回值,字符串或null

//列表操作
$redis->lpush('mylist','crazymus'); //返回值，列表长度
$redis->rpush('mylist','crazymus'); 
$redis->lpop('mylist');//返回值，列表长度
$redis->rpop('mylist');
$redis->lrange('mylist',0,-1); //返回值，数组
$redis->llen('mylist'); //返回值，列表长度
?>
```