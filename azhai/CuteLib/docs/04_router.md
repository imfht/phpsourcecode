
## Router  Web路由

一个简便灵活的WEB路由器。

匹配一个网址的控制器只有两次路由：从入口文件中的根路由到达目标文件子路由；包含子路由文件，

从中找到对应的路由项。

避免包含其他不相干路由文件，PHP在文件系统中包含大量代码文件是个耗时操作，Drupal的boot过程就是一个例子。

PHP的特点是访问一次释放所有资源，并不适合常见的集中管理全部URL，分段映射的方式。


# 使用方法

```php
<?php
//获取当前路由，第一个路由同时也是根路由
$root = \Cute\Web\Router::getCurrent(); //根路由
//指定扫描子路由器规则，当前文件不需要扫描
$root->expose(__DIR__, '*.php');
$root->expose(__DIR__, '*/*.php');

//创建一些路由项到当前路由器，控制器是一个Closure，或者实现了__invoke()方法的类/对象
$root->route('/', function(){ return __FILE__ . ':' . __LINIE__; });
//故意添加两个空控制器
$root->route('/a/b/c/', function(){ return __FILE__ . ':' . __LINIE__; }, null, null);
```

返回数组中含有以下元素（没有找到匹配项时，只有前3项）

* method string 方法名，未匹配时为except
* handlers array 控制器数组，未匹配时为空
* args array 占位符对应值数组
* url string 当前网址
* rule string 匹配正则式
