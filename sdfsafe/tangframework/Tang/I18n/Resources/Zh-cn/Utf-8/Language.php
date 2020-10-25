<?php
return array(
    'The success of the operation!' => '操作成功！',
	//config
	'Save the configuration file contains no data!' => '保存的配置文件没有数据！',
	'Configuration file [%s] does not exist!' => '没有配置文件[%s]',
    'Configuration file failed to load [%s], please check whether the returned array!' => '配置文件[%s]加载失败，请检查是否返回数组!',

    //service
	'The [%s] service does not exist or service name is empty!' => '[%s]服务不存在或者服务名为空！',
	'Class [%s] does not implement the [%s] interface!' => '类[%s]没有实现[%s]接口',
	'Service class [%s] does not implement the register method!' => '服务类[%s]没有实现register方法',

	//manager
	'%s drive already exists!' => '%s驱动已存在',
	'Drivers [%s] not supported!' => '不支持[%s]驱动',
	
	'File not found' => '文件[%s]未找到',
	'Language pack not found'=>'语言包[%s]未找到',
	
	'Query SQL error' => 'SQL错误：%s',
	'Template File not Found!' => '模板文件[%s]未找到',
	'QiNiu driver [%s] Bucket no accessKey or secretKey!' => '七牛云存储驱动[%s]Bucket没有设置accessKey或者secretKey!',
	'UpYun driver [%s] Bucket no user or password!' => '又拍云驱动[%s]Bucket没有设置user或者password!',

	// upload 
	'Upload file size is [%d byte], is greater than the set of [%d byte]!' => '上传的文件大小为[%d byte],大于设定的[%d byte]',
	'Only allowed to upload the [%s] file!' => '只允许上传[%s]类型文件',
	'File upload error 1' => '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值！',
	'File upload error 2' => '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值！',
	'File upload error 3' => '文件只有部分被上传！',
	'File upload error 4' => '没有文件被上传！',
	'File upload error 6' => '找不到临时文件夹！',
	'File upload error 7' => '文件写入失败！',
	'File upload error 8' => '上传的文件被PHP扩展程序中断!',
	'Illegal file upload!' => '非法上传文件',
	'No [%s] file upload!' => '没有[%s]文件上传!',
	'The uploaded file is not a [%s] file!' => '上传的文件不是一个[%s]文件',
	//分页
	'No more pages!' =>'没有更多的页数!',
	'First page' => '第一页',
	'Prev page' => '前一页',
	'Next page' => '下一页',
	'End page' => '末页',
	//IpLocation
	'IP %s location not available!' => '获取不到IP为%s的地理位置!',
    'The CSRF token could not be verified!' =>'CSRF令牌无法验证！'
);