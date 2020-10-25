<?php 
return array(
'ALL_PASS'=>'dd',      //旧版加密密钥
'authkey'=>'ksdfji',                    // 加密密钥
'cookiepre'=>'cp',            // cookie 前缀
'cookiedomain'=> '', 			// cookie 作用域
'cookiepath' => '/',			// cookie 作用路径

/* Cookie设置 */
'COOKIE_EXPIRE'         => 0,    // Coodie有效期
'COOKIE_DOMAIN'         => '',      // Cookie有效域名
'COOKIE_PATH'           => '/',     // Cookie路径
'COOKIE_PREFIX'         => '',      // Cookie前缀 避免冲突

//日志和错误调试配置
'DEBUG'=>true,	//是否开启调试模式，true开启，false关闭
'LOG_ON'=>true,//是否开启出错信息保存到文件，true开启，false不开启
'ERROR_URL'=>'',//出错信息重定向页面，为空采用默认的出错页面，一般不需要修改
'URL_REWRITE_ON' => 2,
'GROUP_DEFAULT'=>'home',
//数据库配置
'DB_HOST'=>'localhost',//数据库主机，一般不需要修改
'DB_USER'=>'root',//数据库用户名
'DB_PWD'=>'',//数据库密码
'DB_PORT'=>3306,//数据库端口，mysql默认是3306，一般不需要修改
'DB_NAME'=>'blog',//数据库名
'DB_CHARSET'=>'utf8',//数据库编码，一般不需要修改
'DB_PREFIX'=>'cp_',//数据库前缀
'DB_PCONNECT'=>false,//true表示使用永久连接，false表示不适用永久连接，一般不使用永久连接

'DB_CACHE_ON' => false,//是否开启数据库缓存，true开启，false不开启
'DB_CACHE_TYPE' => 'FileCache',//缓存类型，FileCache或Memcache或SaeMemcache，
'DB_CACHE_TIME' => 1800,//缓存时间,0不缓存，-1永久缓存,单位：秒

//文件缓存配置
'DB_CACHE_PATH' => './data/db_cache/',//数据库查询内容缓存目录，地址相对于入口文件，一般不需要修改
'DB_CACHE_CHECK' => false,//是否对缓存进行校验，一般不需要修改
'DB_CACHE_FILE' => 'cachedata',//缓存的数据文件名
'DB_CACHE_SIZE' => '15M',//预设的缓存大小，最小为10M，最大为1G
'DB_CACHE_FLOCK' => true,///是否存在文件锁，设置为false，将模拟文件锁,，一般不需要修改

//模板配置
'TPL_TEMPLATE_PATH'=>'./template/',//模板目录，一般不需要修改
'TPL_TEMPLATE_SUFFIX'=>'.html',//模板后缀，一般不需要修改
'TPL_CACHE_TIME'=>0,//二级缓存是否开启，0为关闭，大于0是开启

);
?>