# templater
一个简单够用的PHP模板，连注释一起200行左右。


# 使用方法：

## 在控制器的PHP代码中

```php
<?php
$tpl = new MY_Templater();
$tpl->addGlobal('title ', 'Test Page');
$tpl->addFrameFile('views/test.php');
echo $tpl->render(['a'=>1, 'b'=>2]);
?>
```

## 两个模板文件 入口 views/test.php

```php
<?php $this->extendTpl('layout.php'); ?>

<?php $this->blockStart('title'); ?>
  <title><?= $title ?></title>
<?php $this->blockEnd(); ?>

<?php $this->blockStart('content'); ?>
  <div>a: <?= $a ?></div>
  <div>b: <?= $b ?></div>
<?php $this->blockEnd(); ?>
```

## 布局 views/layout.php

```php
<html>
<head>
  <?= $this->block('title') ?>
</head>
<body>
  <?= $this->block('content') ?>
</body>
</html>
```

