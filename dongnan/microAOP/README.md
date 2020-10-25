microAOP - 简洁而强大的AOP库
-----------------
microAOP 是一个PHP编写的简洁而强大的AOP库，除去空行和注释，有效代码300多行，却实现了很多有用的特性，让PHP也能简单的实现AOP编程。如果你想在已有的项目中使用AOP编程，通过microAOP将会变得非常轻松，你只需要增加你所有需要的切面类，在原有代码改动最少的情况下就可以轻松实现AOP。

安装
------------
microAOP 可以通过 composer 安装，安装步骤非常简单：

1. 通过 composer 下载 microAOP
2. 创建一个 model 类
3. 创建一个切面类
4. 绑定切面类到 model 的实例

### 第1步: 通过 composer 下载 microAOP

使用以下命令从 composer 下载 microAOP:

``` bash
$ composer require dongnan/microaop
```

Composer 会将 microAOP 安装到你的项目中，安装路径： `vendor/dongnan/microaop` 

### 第2步: 创建一个 model 类

``` php
<?php
namespace yournamespace;

class Model {

    public function save() {
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

}
```

### 第3步: 创建一个切面类

``` php
<?php
namespace yournamespace;

class Aspect {

    public function saveBefore($params) {
        echo '------------------------------------------' . PHP_EOL;
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

    public function saveAfter($params) {
        echo '------------------------------------------' . PHP_EOL;
        echo __METHOD__ . ' has been executed' . PHP_EOL;
    }

}
```

### 第4步: 绑定切面类到 model 的实例

``` php
<?php

use microAOP\Proxy;
use yournamespace\Model;
use yournamespace\Aspect;

$model = new Model();

//Just bind it
Proxy::__bind__($model, new Aspect());

$model->save();

```

执行后输出:
```
------------------------------------------
yournamespace\Aspect::saveBefore has been executed
yournamespace\Model::save has been executed
------------------------------------------
yournamespace\Aspect::saveAfter has been executed

```

特性
------------
1. 绑定切面类非常简单，只需要一行代码
2. 一个对象可以同时绑定多个切面类
3. 支持绑定函数，支持所有callable类型
4. 一个对象可以同时绑定多个函数(callable)
5. 绑定函数的触发规则为方法名，也可以是匹配方法名的规则，支持正则表达式
5. 按绑定顺序执行已绑定的切面类中方法和已绑定的函数(callable)，但函数始终在切面类之后执行
6. 触发位置包括执行方法的之前(before)、之后(after)、有异常时(exception)和总是执行(always)
7. 触发执行的切面类方法或函数(callable)都有一个参数，参数是一个数组，包含被代理类类名(class)、被调用方法名(method)、被调用方法的所有参数集合(args)、被调用方法的返回值(return)（正常执行时）和被调用方法的异常信息(exception)（有异常时）
8. 已绑定的切面类和函数(callable)可以随时移除绑定
9. 支持钩子方法(v0.3.0新增)

例子
------------
请参考项目中 examples 目录的内容
