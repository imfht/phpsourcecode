# TookPHP——简洁高效的PHP开发框架
> #### 相关资源
* <b>官方网站 : [永久网络](http://www.19www.com) </b> :point_left:
* <b>在线文档 : [TookPHP开发手册](http://www.kancloud.cn/lajox/tookphp) </b> :point_left:

> #### 学习交流
* 框架交流群：![](https://raw.githubusercontent.com/JackJiang2011/MobileIMSDK/master/preview/more_screenshots/others/qq_group_icon_16-16.png) `41161311` :point_left:
* bug/建议发送至邮箱：`lajox@19www.com`
* 技术支持/合作/咨询请联系作者QQ：`517544292`

# 一、框架特点

* 提供强大的、完整的类库包，满足开发中的项目需求。
* 按需加载运行速度快，高效的核心编译处理机制让系统运行更快。
* 提供丰富的的错误解决方案，让修正代码变得更快速。

* #### TookPHP只为让程序员而生，简单快速上手，简单快速开发应用

# 二、使用方法

> #### 只需要在页首引入该文件，如：

```php
//开启调试模式
define('DEBUG',TRUE);
//显示DEBUG面板
define('DEBUG_TOOL',TRUE);
//模块列表
define('MODULE_LIST','Home,Admin');
//应用目录
define('APP_PATH','./Application/');
//引入框架
require 'tookphp/TookPHP.php';
```

# 三、框架目录结构

    应用部署目录
    ├─index.php
    ├─htaccess.txt
    ├─Application   应用目录（可设置）
    │  ├─Addons 插件目录
    │  ├─Common 公共模块目录
    │  │  ├─Config  配置文件目录
    │  │  ├─Controller  控制器目录
    │  │  ├─Function自定义函数目录
    │  │  │   └─function.php   默认加载的自定义函数文件
    │  │  ├─Hook钩子目录
    │  │  ├─Lang语言包目录
    │  │  │   └─zh-cn.php   语言配置文件
    │  │  ├─Model   模型文件目录
    │  │  ├─Library 库文件目录
    │  │  └─Tag 拓展标签目录
    │  ├─Home   默认模块目录
    │  │  ├─Config  配置文件目录
    │  │  ├─Controller  控制器目录
    │  │  ├─Function自定义函数目录
    │  │  │   └─function.php   默认加载的自定义函数文件
    │  │  ├─Hook钩子目录
    │  │  ├─Lang语言包目录
    │  │  │   └─zh-cn.php   语言配置文件
    │  │  ├─Model   模型文件目录
    │  │  ├─Library 库文件目录
    │  │  └─Tag 拓展标签目录
    │  ├─Temp   临时文件目录
    │  │  ├─Cache   缓存文件目录
    │  │  ├─Compile 编译文件目录
    │  │  ├─Log 日志文件目录
    │  │  └─Table   表字段文件目录
    ├─Static静态文件目录（可设置）
    ├─tookphp   框架核心文件目录
    │  ├─Library框架类库目录
    │  │  ├─DataData
    │  │  ├─Function框架函数目录
    │  │  ├─Took框架核心目录
    │  │  ├─Tool工具包
    │  │  └─Vendor  第三方类库目录
    │  ├─Config 配置文件目录
    │  ├─Data   数据目录
    │  ├─Lang   语言包目录
    │  ├─Tpl框架Tpl目录
    │  └─TookPHP.php框架入口文件
    └─Uploads   上传文件目录
