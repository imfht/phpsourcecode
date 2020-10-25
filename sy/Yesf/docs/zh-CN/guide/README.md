---
title: 综述
lang: zh-CN
---

# Yesf

![Php Version](https://img.shields.io/badge/php-%3E=7.1-brightgreen.svg?maxAge=2592000)
![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.0-brightgreen.svg?maxAge=2592000)
![Packagist](https://img.shields.io/packagist/v/sylingd/yesf-framework.svg)
![license](https://img.shields.io/github/license/sylingd/Yesf.svg)

Yesf是基于Swoole 4.0+的框架。具有以下优点：

* 高性能
* 灵活、扩展能力强
* 单元测试覆盖

Yesf基于Swoole，因此还支持TCP监听、UDP监听、异步任务等功能

## DEMO

此处有一个简单的demo：[sylingd/Yesf-Example](https://github.com/sylingd/Yesf-Example)，您可以结合demo了解如何使用Yesf

## 文档说明

本文档对应Yesf版本为`2.0.0`，如有错误请提交issue至[GitHub](https://github.com/sylingd/Yesf/issues/new)或[Gitee](https://gitee.com/sy/Yesf/issues/new)

## PSR规范

目前遵循以下PSR规范：

* [PSR-1: Basic Coding Standard](https://www.php-fig.org/psr/psr-1/)
* [PSR-3: Logger Interface](https://www.php-fig.org/psr/psr-3/)
* [PSR-4: Autoloader](https://www.php-fig.org/psr/psr-4/)
* [PSR-11: Container interface](https://www.php-fig.org/psr/psr-11/)
* [PSR-16: Simple Cache](https://www.php-fig.org/psr/psr-16/)

## 功能说明

部分功能因为和框架本身关系不大，且已经有很多优秀的第三方库，因此Yesf不再内置它们。建议直接使用composer安装，例如：

* 输入验证：[respect/validation](https://packagist.org/packages/respect/validation) [nette/utils](https://packagist.org/packages/nette/utils)
* 图片验证码：[gregwar/captcha](https://packagist.org/packages/gregwar/captcha) [dapphp/securimage](https://packagist.org/packages/dapphp/securimage)
* 图片处理：[intervention/image](https://packagist.org/packages/intervention/image) [nette/utils](https://packagist.org/packages/nette/utils)
* JWT：[firebase/php-jwt](https://packagist.org/packages/firebase/php-jwt) [lcobucci/jwt](https://packagist.org/packages/lcobucci/jwt)