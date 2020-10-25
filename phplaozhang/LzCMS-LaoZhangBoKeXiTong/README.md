把所有文件放进你的项目目录，
先开启web服务器伪静态
域名访问根据提示安装

伪静态规则配置(可忽略)
1、打开/application/route.php 去掉注释
2、打开后台 内容-》栏目管理-》更新内容链接
3、修改栏目《留言》的链接为：http://域名/feedback.html

v1.1.4更新日志2017-8-15
1、修改设置表数据结构
2、修正后台删除栏目更新栏目缓存问题
3、修正单页模型栏目不能删除问题
4、修正后台列表页小屏幕下错位问题
5、后台可设置留言回复的管理员和姓名
6、修正在IE下上传图片/文件提示下载的问题

v1.1.3更新日志2017-8-7
1、单页模型页面描述内容调整
2、搜狐畅言兼容http和https
3、IIS伪静态隐藏index.php配置
4、修复QQ登陆无效问题


v1.1.2更新日志2017-7-26
1、新增置顶功能 
2、图集多图上传支持多选上传
3、文章支持excel导入和导出excel
4、修复已知bug

v1.1.1更新日志2017-7-18：
1、修复数据库字段必填带来的错误
2、修复apache伪静态配置文件错误导致的 No input file specified

v1.1.0更新日志2017-6-26：
1、增加源码一键安装
2、增强图集模型详情页轮播
3、列表页图片懒加载
4、搜索关键词标红
5、增强前台内容可控性如：首页banner、首页网站理念等可在后台修改
6、部分功能优化
7、部分bug修正

官方网址：http://www.lzcms.top 
老张博客网址：http://www.phplaozhang.com 
记得加友情链接

## 目录结构

~~~
www  WEB部署目录（或者子目录）
├─application           应用目录
│  ├─common             公共模块目录（可以更改）
│  ├─module_name        模块目录
│  │  ├─config.php      模块配置文件
│  │  ├─common.php      模块函数文件
│  │  ├─controller      控制器目录
│  │  ├─model           模型目录
│  │  ├─view            视图目录
│  │  └─ ...            更多类库目录
│  │
│  ├─command.php        命令行工具配置文件
│  ├─common.php         公共函数文件
│  ├─config.php         公共配置文件
│  ├─route.php          路由配置文件
│  ├─tags.php           应用行为扩展定义文件
│  └─database.php       数据库配置文件
│
│
├─thinkphp              框架系统目录
│  ├─lang               语言文件目录
│  ├─library            框架类库目录
│  │  ├─think           Think类库包目录
│  │  └─traits          系统Trait目录
│  │
│  ├─tpl                系统模板目录
│  ├─base.php           基础定义文件
│  ├─console.php        控制台入口文件
│  ├─convention.php     框架惯例配置文件
│  ├─helper.php         助手函数文件
│  ├─phpunit.xml        phpunit配置文件
│  └─start.php          框架入口文件
│
├─static                后台css、js、images文件目录
├─template              前台模版文件目录
├─extend                扩展类库目录
├─runtime               应用的运行时目录（可写，可定制）
├─vendor                第三方类库目录（Composer依赖库）
├─build.php             自动生成定义文件（参考）
├─composer.json         composer 定义文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
├─think                 命令行入口文件
├─index.php             入口文件
├─router.php            快速测试文件
├─.htaccess             用于apache的重写
~~~