<?php
	
/**
 * 文件：config.php
 * 作者：mqycn
 * 博客：http://www.miaoqiyuan.cn
 * 源码：http://gitee.com/mqycn/diy-qrcode/
 * 说明：配置文件(skin1)
 */
 
return array(
	'level' => "Q", //二维码校正级别，可选：L、M、Q、H
	'matrix' => 8, //矩阵的大小， 1-10
	
	'type' => 'jpg', //二维码 输出类型
	
	/**
	 * 图片文件说明：(以上级目录为准)
	 *               [SKIN] : 会替换成 当前 模板 的文件夹
	 */
	'background' => '[SKIN]background.jpg',

	/**
	 * 模版路径说明：(以安装路径 /vendor/qrcode-diy/，域名为 http://localhost/ 为例)
	 *               [WEB_ROOT] : http://localhost/
	 *               [WEB_PATH] : vendor/qrcode-diy/
	 *               [WEB_URI]  : http://localhost/vendor/qrcode-diy/
	 */
	'template' => '[WEB_URI]test.php?qrcode=[KEY]&skin=skin1&page=ad', //落地页路径模版

	'x' => 155, //插入点 X 的位置
	'y' => 601, //插入点 Y 的位置
	'w' => 187, //二维码宽度
	'h' => 187, //二维码高度
);
?>