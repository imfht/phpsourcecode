# simpleframework
PHP SimpleFramework 

library 是框架代码

www.sf.com 是测试框架代码

SimpleFramework, 简约而不简单的MVC框架
	
核心代码小于100K
	简单而高效的核心代码, 相对于CI, 是其框架性能的两倍
	
不用约束任何规则
	规则越多, 意味着学习时间就长, 成本越高
	
配置简单
	无需复杂而又带有约束性的配置
	
容易扩展
	方便自定义各种扩展
	
结构清晰
	目录结构命名清晰明了
	
安全高效
	对于所有的Request过滤


性能测试报告(与 CodeIgniter3.0.5相比, 页面内容一致):

ab -n 1000 -c 500 http://www.sf.com/test

ab -n 1000 -c 500 http://www.ci.com/test

![](http://static.oschina.net/uploads/space/2016/0613/112610_RkjF_1397876.png)