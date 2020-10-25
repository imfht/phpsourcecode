<?php
return array(
	/* 文件上传相关配置 */
	'FILE_UPLOAD' => array(
		'autoSub' => true, //自动子目录保存文件
		'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
		'rootPath' => './Upload/images/admin', //保存根路径
		'waterImg' => './Public/CigoAdminPublic/common/img/water.jpg',//图片水印图片路径
		'waterText' => '我是水印',//文字水印
		'waterTextFont' => './Public/CigoAdminPublic/common/font/msyh.ttf',//文字水印字体路径
		'replace' => false, //存在同名是否覆盖
		'fileLimit' => array(
			'img' => array(
				'maxSize' => 5 * 1024 * 1024,
				'exts' => 'jpg,gif,png,jpeg',
			),
			'video' => array(
				'maxSize' => 10 * 1024 * 1024,
				'exts' => 'mp4,rmvb,mov'
			),
			'file' => array(
				'maxSize' => 5 * 1024 * 1024,
				'exts' => 'doc,zip,rar,txt'
			)
		),
	), //文件上传相关配置（文件上传类配置）
);
