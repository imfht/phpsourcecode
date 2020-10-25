# PHP生成二维码海报，支持多模板


 **增加模板：**

1、qrcode.*** 开头的 文件夹，比如：qrcode.demoABC

2、在第一步创建的文件夹中配置文件config.php，以上面的例子为：qrcode.demoABC/config.php

```php
<?php

return array(
	//二维码部分

	'level' => "L", //二维码校正级别，可选：L、M、Q、H
	'matrix' => 6, //矩阵的大小， 1-10

	'type' => 'png', //二维码 输出类型

	/**
	 * 图片文件说明：(以上级目录为准)
	 *               [SKIN] : 会替换成 当前 模板 的文件夹
	 */
	'background' => '[SKIN]demo.png',

	/**
	 * 模版路径说明：(以安装路径 /vendor/qrcode-diy/，域名为 http://localhost/ 为例)
	 *               [WEB_ROOT] : http://localhost/
	 *               [WEB_PATH] : vendor/qrcode-diy/
	 *               [WEB_URI]  : http://localhost/vendor/qrcode-diy/
	 *               [KEY]      : 必须保留，用于替换最终的邀请码
	 */
	'template' => '[WEB_URI]test.php?qrcode=[KEY]&skin=skin2&page=share', //落地页路径模版

	'x' => 228, //插入点 X 的位置
	'y' => 77, //插入点 Y 的位置
	'w' => 88, //二维码宽度
	'h' => 88, //二维码高度

	//打印文字部分，如果不需要打印文字，下面可省略

	/**
	 * 字体路径说明：(以上级目录为准)
	 *               [SKIN] : 会替换成 当前 模板 的文件夹
	 */
	'font' => '[SKIN]arial.ttf',

	/**
	 * 输出文字说明：
	 *               [KEY]      : 必须保留，用于替换最终的邀请码
	 */
	'text' => '[KEY]',

	'textsize' => 14, //矩阵的大小， 5-50
	'textx' => 138, //文本插入点 X 的位置
	'texty' => 369, //字体基线的位置（字体底部的位置）
	'textcolor' => '#FFFFFF', //字体颜色
);

?>
```
 
 **在线演示：** 

测试地址：http://您的域名/安装路径/test.php

在线测试地址：[http://www.miaoqiyuan.cn/products/diy-qrcode](http://www.miaoqiyuan.cn/products/diy-qrcode)


![输入图片说明](https://gitee.com/uploads/images/2018/0319/221157_c8c162c5_82383.jpeg "111.jpg")

![输入图片说明](https://gitee.com/uploads/images/2018/0319/221148_d9bc0db1_82383.jpeg "222.jpg")

 **二维码样式一：** 

在线测试：[http://www.miaoqiyuan.cn/apps/qrcode/Z2l0ZWUuY29t/skin1.png](http://www.miaoqiyuan.cn/apps/qrcode/Z2l0ZWUuY29t/skin1.png)

![输入图片说明](https://gitee.com/uploads/images/2018/0319/220854_93a1ccc7_82383.png "skin1.png")


 **二维码样式二：** 

在线测试：[http://www.miaoqiyuan.cn/apps/qrcode/Z2l0ZWUuY29t/skin2.png](http://www.miaoqiyuan.cn/apps/qrcode/Z2l0ZWUuY29t/skin2.png)

![输入图片说明](https://gitee.com/uploads/images/2018/0319/220903_d3a72829_82383.png "skin2.png")

