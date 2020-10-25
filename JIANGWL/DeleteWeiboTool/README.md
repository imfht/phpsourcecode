# DeleteWeiboTool

自动化删除所有微博工具


利用该工具可以实现删除个人所有微博功能，不用收到第三方调用API次数限制


不能保证工具长期有效

# 使用方法

配置好代码根目录下的`Config/config.php`中要求必填的项目


`$config['sina_cookie'] `：登陆好后，打开F12开发者工具获取REQUEST中的cookie值填入


`$config['self_page_url'] `：打开开发者工具设置模拟IPHONE6访问页面`m.weibo.cn`,点击查看我的所有微博，复制页面URL



然后开始运行


``` bash
cd /xxx/xxx/DelWeibo
php Delete.php
``` 

详细教程地址：[工具教程](http://www.jwlchina.cn/2016/09/17/%E5%BC%80%E6%BA%90%E9%A1%B9%E7%9B%AE%EF%BC%9A%E5%88%A0%E9%99%A4%E5%BE%AE%E5%8D%9A%E5%B7%A5%E5%85%B7/)
