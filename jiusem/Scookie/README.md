### Scookie
安全的cookie存储方案，可以替代session。在网站需要应对高并发时，使用cookie可以显著提高效率，并解决部署负载均衡造成的session共享问题。但安全是使用cookie时需要考虑的，Scookie的目标就是为了解决这一问题。
#### 特性

- 加密存储;
- 自定义密钥;
- 支持数组;

#### 使用
打开Scookie.class.php,设置你的密钥：
```php
<?php
private static $key = 'YOUR KEY';
?>
```
引入：
```php
<?php
require('Scookie.class.php');
?>
```
设置cookie：
```php
<?php
Scookie::set('user','123'); //存储字符串

Scookie::set('user',array(
	'id'=>1,
	'name'=>'crazymus'
)); //存储数组

Scookie::set('user','123',array(
	'expire'=>time()+3600, //有效期一小时
	'path'=>'/' //对所有目录有效
	'domain'=>'www.test.com' //可访问域名
));
?>
```
读取cookie：
```php
<?php
Scookie::get('user'); //若不存在，则返回null
?>
```


