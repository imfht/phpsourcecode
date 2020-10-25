#PHPCrazy

PHPCrazy 是一个基于PHP编写的建站程序，程序简单，只有登录、注册、和后台管理功能

当然您也可以通过开源项目主页下载插件：[Crazy-code](http://code.zhangyun.org)

##Hello,World!
下面例子教你如何创建一个Hello,world插件：
###控制层
创建控制层文件 ```world.php``` ,把他放在 ```includes/controller/hello/``` 目录下（没有 ```hello``` 目录请新建一个）, 代码如下:
```php
<?php

// 定义网页标题
$PageTitle = 'hello world';

// 加载 helloworld 模版
include T('helloworld');

?>
```

###视图
创建视图文件 ```helloworld.tpl.php``` ,把他放在 ```themes/AmazeUI/``` 目录下, 代码如下:
```php
<h1>Hello,World!</h1>
```

###访问试试？
```
http://您的域名/index.php/hello:world/
```
###您是开发者？
如果您想二次开发PHPCrazy，您可能喜欢以下工具：

[Sublime Text](https://www.baidu.com/s?wd=sublime+text) 优雅的代码编辑器

[Crazy / PHPCrazy Sublime Text Snippets](http://git.oschina.net/Crazy-code/PHPCrazy-Sublime-Text-Snippets) Sublime Text编辑器下的PHPCrazy代码补全插件

[Crazy / Amaze UI Snippets for Chinese](http://git.oschina.net/Crazy-code/Amaze-UI-Snippets-for-Chinese) Sublime Text编辑器下的 Amaze UI 代码自动补全插件
##开发人员   
@[Crazy](http://zhangyun.org) 创始人   
@[yi小轩](http://maogu.cc) UI设计