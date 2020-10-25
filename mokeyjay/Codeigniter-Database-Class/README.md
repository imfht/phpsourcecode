> **请注意：**
> 此项目已转移至 Github
> 这里将不再继续维护，请移步至 → [mokeyjay/Codeigniter-Database-Class](https://github.com/mokeyjay/Codeigniter-Database-Class)

##Codeigniter-Database-Class是什么？
是由[超能小紫](http://www.mokeyjay.com)出品的数据库操作类

（上面应有删除线）（谁能教我MD的删除线怎么打）（波浪号不管用）

其实只是小紫基于`CodeIgniter` 提取、修改而来的数据库操作类

> 现已跟进CI官方更新至 3.1.0

因为是基于最新的3.1.0重新提取的，暂且算是测试版

如果有BUG，请点击上方的`Tags`或`master`，选择旧版本下载使用。并向我反馈此BUG

##为什么整这个玩意儿？

因为工作需要，有时要写些小型Web APP。因此我需要一个非常简单的MVC框架

是的，得比`CodeIgniter`更简单、更高效、更顺手

于是我一边开发一边总结，搞了一套MVC框架，名为`RemiliaPHP`，成熟后开源（是的没错就是`蕾米莉亚`）

但是每次手写SQL很蛋疼啊，我需要SQL Builder帮我解决这麻烦事

于是潜心开发了`172800`秒（四舍五入就是一个亿），终于算是比较完善地实现了CURD语句的生成

但还是不够完善（喂！）

于是我一怒之下把CI的数据库类抠了出来，做了些修改，以便能够在CI外部使用

##修改了哪些地方？

* `全局` : 不再使用视图显示异常，而是直接`throw`抛出`CI_DB_Exception`，方便捕捉处理
* `DB.php` : 3-5 添加了两个常量，可根据实际情况修改；引入空的`CI_DB_Exception`类
* `DB.php` : 185 声明了`ci_db_is_php`和`ci_db_log_message`函数，可根据实际情况修改
* `DB_driver.php` : 1740 数据库错误函数，去掉了页面模板，改为抛出异常
* `DB_lang.php` 从CI的多语言文件（中文）中抠出了数据库的部分
* `DB_config.php` 数据库配置文件。可以根据需要放置到其他目录，但别忘了修改`DB.php`中的常量哦
* 附赠 [数据库配置教程](http://codeigniter.org.cn/user_guide/database/configuration.html)
* 将一些数据库类需要用到的CI内置函数独立出来，并添加`ci_db_`前缀防止冲突
* 修改文件加载部分的代码（毕竟文件结构改了）

##如何使用？
```php
<?php
    require 'DB.php';
    $db = &DB();
?>
```
现在，你就可以用$db进行数据库操作了，类似CI中的this->db。例如：
```php
<?php
    $db->select('value')->get_where('options', ['name'=>'site_url'], 1)->result_array();
?>
```

如有疑问请看 Codeigniter官方中文文档 [数据库参考](http://codeigniter.org.cn/user_guide/database/index.html)

##版权声明
本项目`Codeigniter-Database-Class`提取自PHP开源框架`CodeIgniter`

`CodeIgniter`使用[MIT](http://opensource.org/licenses/MIT)开源协议，请自觉遵守

版权归`CodeIgniter`所有，请务必保留版权以及许可声明

##有问题反馈
在使用中有任何问题，欢迎反馈给我，可以用以下联系方式跟我交流

* 博客：[超能小紫](http://www.mokeyjay.com)
* 微博: [mokeyjay](http://weibo.com/mokeyjay)
* 邮件(mokeyjay#126.com)

##顺便一提

[MaHua在线markdown编辑器](http://mahua.jser.me/) 挺好用的。实时显示效果，你现在所看到的md页面就是我在这上面写出来的

##结语

哈哈哈哈哈终于搞定啦不用费劲儿自己写操作类啦可以省下时间去玩 **守望屁股** 咯！各位拜拜~