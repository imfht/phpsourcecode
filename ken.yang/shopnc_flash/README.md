## 数据闪存

一个shopnc的扩展类，可以用于Form表单提交错误，返回时展示提交的数据<br>
简单修改就可以用于其他任何PHP程序

## 可扩展性

可以方便的书写实现方法进行其他方式扩展，比如缓存、数据库等


## 使用方法

以form表单提交为例,仅仅作为演示，需要根据自身需求修改

1、form表单input的value设置Flash::get('name');
```
<input value="<?php echo Flash::get('name')?>">
```

2、提交表单后，保存闪存数据
```
Flash::setAll($_POST);
```

3、表单提交成功，闪存闪存数据
```
Flash::clearAll($_POST);
```

## 备注

1、form表单成功提交后应该清除闪存
2、闪存数据使用一次后及清除

## 版本更新

#### V0.2

1、增加check方法，可以用于三元运算符

2、增加基于shopnc的cache类扩展

#### V0.1

1、发布第一个版本，默认仅有session扩展类

2、session模式下支持任意PHP程序