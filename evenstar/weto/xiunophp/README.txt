XiunoPHP
========

XiunoPHP 是一款面向高负载应用的 PHP 开发框架，PHPer 通过它可以快速的简单的开发出高负载项目。

它诞生于 NoSQL 刚刚兴起的时代，从开始就良好的支持 NoSQL DB，比如 MongoDB，当让也可以通过添加驱动文件来支持其他类型的DB。

它是 Xiuno BBS 产品开发过程中的衍生品，只有340K，34个文件，它良好的封装了各种DB（MySQL、MongoDB...), CACHE(Memcached、TTServer、Redis...），对上层只提供了12个方法，只需要掌握这12个方法，开发者就可以任意操作各种DB,CACHE。

它以行为最小存储单位，这样大大的简化和统一了 DB,CACHE 的接口，并且它引入了单点分发自增ID，让应用不再依赖于DB的 count(), max()，函数，便于分布式程序的设计。

您可以像这样使用它：

	// ------------ 操作 db ---------- //
	
	您可以像这样读取db中的数据:
	$user = $db->get("user-uid-123");
	
	更新用户数据:
	$db->set("user-uid-123", $user);
	
	删除一条记录：
	$db->delete("user-uid-123");
	
	统计数据:
	$n = $db->count('user');
	
	// ------------ 操作 cache ---------- //
	
	读取 Cache 中的数据：
	$user = $cache->get("user-uid-123");
	
	更新 Cache 中的数据：
	$cache->set("user-uid-123", 123);
	
	删除一条记录：
	$cache->delete("user-uid-123");
	
	统计:
	$n = $cache->count('user');

看起来是不是太简单了，确实是太简单了，从此不必再记忆各种SQL语法，这样顺便还消灭了LEFT JOIN等容易产生性能问题的SQL语句产生的机会。
高负载，安全性，分布式增加了程序的复杂性，而这个框架就是通过一些约定来消除这些复杂性，我们强烈向开发者推荐这个框架。

在线文档地址: http://php.xiuno.com/