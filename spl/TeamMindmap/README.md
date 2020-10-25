# 项目说明

## 简介
将思维导图运用到团队协作中, 并提供在线的思维导图绘制功能.

## 相关技术点
* 项目后端采用[Laravel](http://laravel.com) 4.2构建
* 前端使用[Twitter Bootstrap](http://getbootstrap.com)作为UI基本构件，[AngularJS](https://angularjs.org)被应用于编写Web应用页面
* AngularJS与Laravel采用REST风格的数据交互
* 思维导图的在线绘制基于[RaphaelJS](http://raphaeljs.com)（一款使用SVG W3C标准的JavaScript矢量图形库)


## 初始化步骤
**为成功初始化，必须安装[Composer](http://getcomposer.org)、[Bower](http://bower.io)、[Grunt](http://gruntjs.com)和[Node.js](http://gruntjs.com)环境**

在命令行/终端输入下列指定，或进行相关操作：

* `composer install` : 此命令用于通过Composer安装Laravel框架本身，以及项目所依赖的第三方类或本项目所延伸的子项目
* `npm install` : 此命令用于安装npm包，描述如下：
    * grunt　与　grunt-* 用于构建前端的CSS文件和JS文件，如想部署项目必须安装
    * karma 与　karma-* 用于前端的自动化测试，如不进行相关开发则无需安装
* `bower install`　：　此命令用于通过bower安装前端用到的第三方类库或本项目所延伸的子项目
* `grunt` : 此命令用于执行Ｇrunt，用于构建生产环境所用的各类前端文件
* 设置数据库链接，可以参考 [这里](http://v4.golaravel.com/docs/4.2/database#configuration) 和 [这里](http://v4.golaravel.com/docs/4.2/configuration#environment-configuration) 进行配置
* 执行数据库迁移： `php artisan migrate`

## 备注
* 为避免冲突和保护开发者信息，请在命令行中使用下列指令（该指令将假设文件无改动，只能应用于版本库中已跟踪的文件）：
  * 假定不改动， 以设置database.php为例：`git update-index --assume-unchanged app/config/database.php` 
  * 取消设置： `git update-index --no-assume-unchanged app/config/database.php`
* 更多相关信息，请参考 [项目Wiki](http://git.oschina.net/spl/TeamMindmap/wikis/home)