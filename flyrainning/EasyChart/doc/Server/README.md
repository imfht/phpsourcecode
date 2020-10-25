# EasyChart Server

服务器端PHP实现

## 使用方法

在php项目中包含`Server/loader.php`文件

```
<?php
require 'EasyChart/dist/Server/loader.php';
?>
```

## config

全局配置，详情[config](config.md)

## EasyChart 类

实现EasyChart的主要功能，详情[EasyChart](EasyChart.md)

## 可用图表

- Bar
- Line
- Pie
- Gauge
- Bar3D
- Line3D

## 新的图表类型

可以通过插件的形式让EasyChart支持更多的图表类型

相关说明参考[develop](develop.md)
