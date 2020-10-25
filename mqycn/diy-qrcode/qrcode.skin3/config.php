<?php

/**
 * 文件：config.php
 * 作者：mqycn
 * 博客：http://www.miaoqiyuan.cn
 * 源码：http://gitee.com/mqycn/diy-qrcode/
 * 说明：配置文件(skin3)
 */

return array(

	//二维码部分

	'level' => "L", //二维码校正级别，可选：L、M、Q、H
	'matrix' => 6, //矩阵的大小， 1-10

	'type' => 'png', //二维码 输出类型

	/**
	 * 图片文件说明：(以上级目录为准)
	 *               [SKIN] : 会替换成 当前 模板 的文件夹
	 */
	'background' => '[SKIN]bg.jpg',

	/**
	 * 模版路径说明：(以安装路径 /vendor/qrcode-diy/，域名为 http://localhost/ 为例)
	 *               [WEB_ROOT] : http://localhost/
	 *               [WEB_PATH] : vendor/qrcode-diy/
	 *               [WEB_URI]  : http://localhost/vendor/qrcode-diy/
	 *               [KEY]      : 必须保留，用于替换最终的邀请码
	 */
	'template' => '[WEB_URI]test.php?qrcode=[KEY]&skin=skin3&page=skin3', //落地页路径模版

	'x' => 65, //插入点 X 的位置
	'y' => 125, //插入点 Y 的位置
	'w' => 190, //二维码宽度
	'h' => 190, //二维码高度

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