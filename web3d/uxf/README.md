# Uxf PHP MVC框架说明

uxf - User eXtensible Framework。此框架基于Discuz环境运行。这是一个很NB的名字，但其实功能特性一直没有明确的定位，所以一直没有以明确的版本号形式发布。

## 框架开发目的

  * 提供完整的MVC编程范式；


## 框架设计原则

  1. 便于团队协作
  2. 使代码便于移植
  3. 提倡面向对象式编程思维
  4. 尽量不改动Discuz，保持原系统干净可升级

## 主要功能特性

  * 模块化代码组织结构

模块目录在DISCUZ_ROOT . 'source/modules' 下，每个文件夹代表一个模块，默认 common。

  * 统一路由构造与解析

比如模版中统一的url写法

```
{url:'/api-main/list/type/linux/order/1/name/'.$val.name}
```
参数说明：

  * api:模块，默认模块common可不用添加，如 /news/list/id/10
  * main:控制器
  * list:动作
  * 其他：都是参数

在普通url模式下，上述写法将会生成

```
idx.php?module=api&mod=main&action=list&type=linux&order=1&name=Ubuntu
```
这样的完整的url。

在pathinfo的模式下，将会生成
```
/api-main/list/type/linux/order/1/name/Ubuntu
```

在Diy的模式下，将根据自己定义的方式生成，如
```
/api/linux/Ubuntu
```

## 使用场景

  * 基于discuz运行的大量已有社区网站，需要扩展功能时，以相对独立的方式进行功能开发，使用本框架可以获得比较完整的框架特性、较高的开发效率
  * 学习使用Discuz的大量组件进行系统开发时，通过参考本系统，获得一定的灵感

## 系统部署

### 伪静态设置

  1. Apache服务器
  2. Nginx服务器


