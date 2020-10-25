# 常量
参考:http://php.net/manual/zh/language.constants.php

## 使用
``` php
define('MAXSIZE', 100);
//判断
defined('MAXSIZE');//true
//取值
echo constant('MAXSIZE');//100

const MINSIZE = 99;
//判断
defined('MINSIZE');//true
//取值
echo constant('MINSIZE');//99
```

## 区别
```
使用 const 关键字定义常量必须处于最顶端的作用区域，因为此方法是在编译时定义的。
这就意味着不能在函数内（类方法内），循环内以及 if 语句之内用 const 来定义常量。
```

## 其它
``` php
get_defined_constants — 返回所有常量的关联数组，键是常量名，值是常量值
```