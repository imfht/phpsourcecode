---
title: 插件
lang: zh-CN
---

# 综述

为了在不破坏框架整体流程的前提下，保证灵活度，框架有“插件”功能。插件可以在一些特殊事件时触发，根据用户需要进行一些修改。

在插件中可以使用协程，但请注意判断当前是否为Task进程。Task进程没有创建协程上下文，因此无法使用协程。

## 插件总体规则

#### 先注册先调用

先注册的插件将会被先调用

#### 单一返回

一个事件可以注册多个插件。触发时，会循环调用每个插件，直到返回结果不为NULL。例如：
```
class PluginTest  {
  public static $isTrigger = 0;
  public static function callback1($data) {
    self::$isTrigger = 1;
    return NULL;
  }
  public static function callback2($data) {
    return '_t_' . $data;
  }
  public function testSeveralPlugin() {
    Plugin::register('test', 'PluginTest::callback2');
    Plugin::register('test', 'PluginTest::callback1');
    echo Plugin::trigger('test', ['_test_data_']); //输出：_t__test_data_
    echo self::$isTrigger; //输出：0
  }
}
```

