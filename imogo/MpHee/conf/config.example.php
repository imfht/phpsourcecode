<?php
return array(
		//重写规则
		'REWRITE' => array(
				//'<app>/<c>/<a>.html' => '<app>/<c>/<a>',
			),
			
		'APP' => array(
				//日志和错误调试配置
				'DEBUG' => true,	//是否开启调试模式
				'LOG_ON' => false,//是否开启出错信息保存到文件
				'LOG_PATH' => BASE_PATH . 'cache/log/',//出错信息存放的目录
				'ERROR_URL' => '',//出错信息重定向页面，为空采用默认的出错页面
				'URL_HTTP_HOST' => '', //设置网址域
				'TIMEZONE' => 'PRC', //时区设置
			),
			
		//数据库配置
		'DB'  => array(								
				'DB_TYPE' => 'mysql',//数据库类型，一般不需要修改
				'DB_HOST' => 'localhost',//数据库主机，一般不需要修改
				'DB_USER' => 'root',//数据库用户名
				'DB_PWD' => '123456',//数据库密码
				'DB_PORT' => 3306,//数据库端口，mysql默认是3306，一般不需要修改
				'DB_NAME' => 'cpapp',//数据库名
				'DB_CHARSET' => 'utf8',//数据库编码，一般不需要修改
				'DB_PREFIX' => 'cp_',//数据库前缀
			),
		
		//模板配置
		'TPL' => array(				
				'TPL_TEMPLATE_PATH' => BASE_PATH,//模板目录，一般不需要修改
				'TPL_TEMPLATE_SUFFIX'=>'.php',//模板后缀,一般不需要修改
				'TPL_CACHE_ON'=> true ,//是否开启模板缓存，true开启,false不开启
				'TPL_CACHE_TYPE'=>'',//数据缓存类型，为空或Memcache或SaeMemcache，其中为空为普通文件缓存，cp2.0添加
				
				//普通文件缓存
				'TPL_CACHE_PATH'=> BASE_PATH . 'cache/tpl_cache/', //模板缓存目录,一般不需要修改
				'TPL_CACHE_SUFFIX'=>'.php',//模板缓存后缀,一般不需要修改
				
				//memcache配置，cp2.0添加
				'MEM_SERVER' => array( array('127.0.0.1', 11211),  array('127.0.0.2', 11211)),
				'MEM_GROUP' => 'tpl',
				
				//SaeMemcache配置，cp2.0添加
				'SAE_MEM_GROUP' => 'tpl',
			), 
);