#PHPCrazy.Class.QRcode
PHPCrazy 二维码生成类

##如何使用？
下载[ Crazy / PHPCrazy.Class.QRcode](http://git.oschina.net/Crazy-code/PHPCrazy.Class.QRcode/repository/archive/master)，解压到PHPCrazy根目录下

##实例
###
```php
<?php 
// 待生成二维码的字符
$text = 'http://53109774.qzone.qq.com';

// 保存路径
// false 为直接输出图片
// ./qrcode.png为把图片保存为当前目录下，名为qrcode.png
$filename = false;

// 像素
$px = 4;

// 图片尺寸
$size = 4;

// 范围从 0（最差质量，文件更小）到 100（最佳质量，文件最大）。默认为 IJG 默认的质量值（大约 75）。
$quality = 85;

QRcode::png($text, $filename, $px, $size, $quality);
?>
```