<?php
return array(
	//'配置项'=>'配置值'
		'TMPL_L_DELIM'=>'<{', //修改左定界符
		'TMPL_R_DELIM'=>'}>', //修改右定界符
		
		//'TMPL_EXCEPTION_FILE'=>'./Data/resource/system/exception.html', //访问不存在模块或者方法的异常错误页面
		'SHOW_PAGE_TRACE'=>false, //开启页面Trace;
		'TMPL_TEMPLATE_SUFFIX'=>'.html',//更改模板文件后缀名
		
		'TMPL_FILE_DEPR'=>'_',//修改模板文件目录层次,主题模板目录/模块_方法.html
		
		'MODULE_ALLOW_LIST' => array('Home','Mobile','Manage'),// 允许访问的模块列表
		'MODULE_DENY_LIST'   => array('Common'),// 禁止访问的模块列表
		'DEFAULT_MODULE' => 'Home', //默认分组
		
// 		'APP_GROUP_LIST'=>'Home,Admin,Wap', //项目分组设定
// 		'DEFAULT_GROUP'=>'Home', //默认分组
		
		'TAGLIB_LOAD'=>true,//加载标签库打开
		'APP_AUTOLOAD_PATH'=>'@.TagLib', //自动载入当前项目TagLib文件夹下的文件
		'TAGLIB_BUILD_IN'=>'Cx,Tuzi', //载入自定义Tuzi标签库
		
		'USER_DATA_PATH' => './Data/Backupdata/',//系统备份数据库时每个sql分卷大小，单位字节 //5M=5*1024*1024=5242880
		'CFG_SQL_FILESIZE' => 5242880, //值不可太大，否则会导致内存溢出备份、恢复失败，合理大小在512K~10M间，建议5M一卷
		
		'DB_PATH_NAME'=> 'db',        //备份目录名称,主要是为了创建备份目录
		'DB_PATH'     => './db/',     //数据库备份路径必须以 / 结尾；
		'DB_PART'     => '20971520',  //该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M
		'DB_COMPRESS' => '1',         //压缩备份文件需要PHP环境支持gzopen,gzwrite函数        0:不压缩 1:启用压缩
		'DB_LEVEL'    => '9',         //压缩级别   1:普通   4:一般   9:最高
		
		//安全处理
		'DEFAULT_FILTER'=>'htmlspecialchars,strip_tags',//配置函数过滤
		'VAR_FILTERS'=>'filter_default,filter_exp',//安全过滤

		'TOKEN_ON'=>false,  // 是否开启令牌验证
		'TOKEN_NAME'=>'__hash__',    // 令牌验证的表单隐藏字段名称
		'TOKEN_TYPE'=>'md5',  //令牌哈希验证规则 默认为MD5
		'TOKEN_RESET'=>true,  //令牌验证出错后是否重置令牌 默认为true
		'DB_FIELDTYPE_CHECK'=>true,  // 开启字段类型验证
		
		//'READ_DATA_MAP'=>true,//设置开启字段映射
		
		//加载其他配置文件　
		'LOAD_EXT_CONFIG' => 'config_db,config_url,config_pctime,config_mbtime,config_theme,config_setting,config_fenye',//加载扩展配置文件
		
);

