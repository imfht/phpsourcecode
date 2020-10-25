---
title: 安装
lang: zh-CN
---

# 安装

## 环境需求

* Swoole 4.0+
* PHP 7.1+
* Composer

### 可选功能

* 日志功能建议安装[SeasLog扩展](https://seasx.github.io/SeasLog/)
* 热重载（仅开发模式存在）需要[inotify扩展](https://pecl.php.net/package/inotify)

## 从模板创建

* 运行`composer create-project sylingd/yesf`

或

* 下载[sylingd/Yesf-Template](https://github.com/sylingd/Yesf-Template)
* 运行`composer install`

## 编写你的程序

按照示例，编写Controller、Model和其他代码。并确保配置正确，即可运行。

### 修改默认命名空间

默认命名空间是`App`，若需要修改，请修改下列地方：

* composer.json中的autoload
* app/Config/Project.php中的`namespace`
* app目录下所有类的命名空间