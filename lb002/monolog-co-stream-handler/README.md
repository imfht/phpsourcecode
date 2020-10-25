# monolog-co-stream-handler

[![Build Status](https://travis-ci.org/luoxiaojun1992/monolog-co-stream-handler.svg?branch=master)](https://travis-ci.org/luoxiaojun1992/monolog-co-stream-handler)

## 中文版

### 描述
Monolog coroutine stream handler based on swoole coroutine.

### 环境依赖
1. Swoole2.1.0+ (编译时请添加参数 --enable-openssl --enable-coroutine)
2. PHP7.1+
3. 勿同时安装opencensus扩展，经测试有内存泄漏问题

### 安装

```shell
composer require "luoxiaojun1992/monolog-co-stream-handler:*"
```

或添加 requirement 到 composer.json

```json
{
  "require": {
    "luoxiaojun1992/monolog-co-stream-handler": "*"
  }
}
```

### 使用示例
请参考 tests/HandlerTests.php

## English Version

### Description
Monolog coroutine stream handler based on swoole coroutine.

### Requirements
1. Swoole2.1.0+ (compile with arguments --enable-openssl --enable-coroutine)
2. PHP7.1+
3. Don't install opencensus extension to avoiding memory leak.

### Installation

```shell
composer require "luoxiaojun1992/monolog-co-stream-handler:*"
```

or add a requirement to composer.json

```json
{
  "require": {
    "luoxiaojun1992/monolog-co-stream-handler": "*"
  }
}
```

### Usage
Please see tests/HandlerTests.php.
