<?php
    if (!defined('THINK_PATH')) exit();
    $array=array(    			
		//* 模板相关配置 */
		'TMPL_PARSE_STRING' => array(
			'__INS__' => __ROOT__ . '/Public/Ins',
			'__STATIC__' => __ROOT__ . '/Public/Static',
			'__ADDONS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Addons',
			'__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
			'__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
			'__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
		),
		'TMPL_NO_HAVE_AUTH'=>APP_PATH.MODULE_NAME.'/View/Public/no_have_auth.html',	
		'EMP_PIC_PATH'=>'Uploads/emp_pic/',
		'TEMPLETE_PATH'=>'./Uploads/Templete',	
	    		  
		 // 文件上传相关配置 
		'CHUNK_UPLOAD'=>true,
	    'DOWNLOAD_UPLOAD' => array(
	        'mimes'    => '', //允许上传的文件MiMe类型
	        'maxSize'  => 0, //上传的文件大小限制 (0-不做限制)
	        'autoSub'  => true, //自动子目录保存文件
	        'subName'  =>  array('date','Y-m'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
	        'rootPath' => './Uploads/Download/', //保存根路径
	        'savePath' => '', //保存路径
	        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
	        'saveExt'  => '', //文件保存后缀，空则使用原后缀
	        'replace'  => false, //存在同名是否覆盖
	        'hash'     => true, //是否生成hash编码
	        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
	    ), 
	    
	    //下载模型上传配置（文件上传类配置）
		'UPLOAD_FILE_EXT'=>'ppt,pptx,xls,xlsx,jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,pdf', //允许上传的文件后缀
		'SYSTEM_CONFIG'=>'基本设置,安全设置,微信设置,记账-收入,记账-支出,'
	 );
    return $array;
?>