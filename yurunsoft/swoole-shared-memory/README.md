# swoole-shared-memory

[![Latest Version](https://img.shields.io/packagist/v/yurunsoft/swoole-shared-memory.svg)](https://packagist.org/packages/yurunsoft/swoole-shared-memory)
[![Php Version](https://img.shields.io/badge/php-%3E=7.1-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.1.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![IMI License](https://img.shields.io/github/license/Yurunsoft/swoole-shared-memory.svg)](https://github.com/Yurunsoft/swoole-shared-memory/blob/master/LICENSE)

## 介绍

`Swoole Shared Memory` ( 以下简称 `SSM` ) 是为了解决 `Swoole` 常驻内存场景，多进程变量共享问题而开发的组件。

`SSM` 直接支持任意变量的跨进程共享，它是通过序列化和反序列化实现的。不仅支持常用的 `KV` 操作，还支持`Stack`、`Queue` 和 `PriorityQueue`数据结构操作。

`SSM` 通过 `Unix Socket` 内核通信，不走网卡，效率极高。无需预先定义空间大小、字段等，甚至可以与 `fpm` 项目进行变量共享 ( `fpm` 项目仅可作为客户端 )。

你只需要在 `Swoole` 自定义进程中启动 `SSM` 服务来监听 `Unix Socket`，或者也可以启动一个独立的 `SSM` 服务。

## Composer

本项目可以使用composer安装，遵循psr-4自动加载规则，在你的 `composer.json` 中加入下面的内容:

```json
{
    "require": {
        "yurunsoft/swoole-shared-memory": "~1.0"
    }
}
```

然后执行 `composer update` 安装。

## 文档

[API 文档](https://apidoc.gitee.com/yurunsoft/swoole-shared-memory) (感谢码云提供服务)

QQ群：17916227 [![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题会有人解答和修复。

## 运行环境

- [PHP](https://php.net/) >= 7.1
- [Composer](https://getcomposer.org/)
- [Swoole](https://www.swoole.com/) >= 4.1.0

## 版权信息

Swoole Shared Memory 遵循 MIT 开源协议发布，并提供免费使用。

## 捐赠

<img src="https://raw.githubusercontent.com/Yurunsoft/swoole-shared-memory/dev/res/pay.png"/>

开源不求盈利，多少都是心意，生活不易，随缘随缘……
