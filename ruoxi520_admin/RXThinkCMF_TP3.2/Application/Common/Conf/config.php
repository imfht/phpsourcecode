<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 系统公共配置
 * 
 * @author 牧羊人
 * @date 2018-08-27
 */

//引入自定义配置文件
$configArr = include 'config.inc.php';

//获取数据库配置
$dbConfig = $configArr['DB_CONFIG'];
$itemArr = explode('://', $dbConfig);
$dbType = $itemArr[0];
list($dbUser, $dbPwd, $dbHost,$dbPort,$dbName) = preg_split("/[:@\/]/",$itemArr[1]);

//【数据库常规配置】
$db = array(
    'DB_TYPE'   => $dbType, // 数据库类型
    'DB_HOST'   => $dbHost, // 服务器地址
    'DB_NAME'   => $dbName, // 数据库名
    'DB_USER'   => $dbUser, // 用户名
    'DB_PWD'    => $dbPwd, // 密码
    'DB_PORT'   => $dbPort, // 端口
    'DB_PARAMS' =>  array(), // 数据库连接参数
    //'DB_PREFIX' => 'yk_', // 数据库表前缀
    'DB_SUFFIX' => '', // 数据库表后缀
    //'DB_CHARSET'=> 'utf8mb4', // 字符集
    'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDTYPE_CHECK' => false, // 是否进行字段类型检查
    'DB_FIELDS_CACHE'  => true, // 启用字段缓存
    'DB_DEPLOY_TYPE' => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE' => false, // 数据库读写是否分离 主从式有效
);

//【系统常规设置】
$common = array(
    
    // 是否显示调试面板
    'SHOW_PAGE_TRACE' =>  False,
    
    //【设置URL调度模式(默认),设置URL不区分链接大小写】
    'URL_CASE_INSENSITIVE'  =>  false,   // 默认false 表示URL区分大小写 true则表示不区分大小写
    
    //【加载扩展配置文件】
    'LOAD_EXT_CONFIG' => 'systemConfig,smsConfig,config.inc',//扩展配置可以支持自动加载额外的自定义配置文件
    
    //【配置扩展函数文件】
    'LOAD_EXT_FILE' => 'Zeus,Logger,Config,SMS',//加载自定义公共函数文件
    
    //【允许访问的模块】
    'MODULE_ALLOW_LIST' =>  array('Home','Admin','API'),
    
    //【禁止访问的模块】
    'MODULE_DENY_LIST' => array('Common','Runtime','Service'),
    
    //【默认参数过滤方法 用于I函数...】
    'DEFAULT_FILTER' =>  'htmlspecialchars,trim,strip_tags',
    
    //【SESSION设置】
    'SESSION_AUTO_START'=>true,// 是否自动开启Session
    
    //【URL重写模式：】
    'URL_MODEL' => 	2,//设置url模式为rewrite重写模式
    
    //【伪静态后缀】
    //'URL_HTML_SUFFIX'=>'html|shtml|xml',	//URL伪静态后缀设置(默认为 .html,可以设置为空)
    
    //【设置系统默认访问路径】
    'DEFAULT_MODULE'        =>  'Home',  // 默认模块
    'DEFAULT_CONTROLLER'    =>  'Index', // 默认控制器名称
    'DEFAULT_ACTION'        =>  'index', // 默认操作名称
    
    //【设置模板替换标记】
    'TMPL_PARSE_STRING' =>  array(
        '__OSS__'           => OSS_URL,
        '__PUBLIC__'        => OSS_URL.__ROOT__.'/Public',
        '__HOME_CSS__'       => __ROOT__.'/Public/Home/css',
        '__HOME_JS__'        => __ROOT__.'/Public/Home/js',
        '__HOME_IMAGES__'    => OSS_URL.'/Public/Home/images',
        '__ADMIN_CSS__'      => __ROOT__.'/Public/Admin/css',
        '__ADMIN_JS__'       => __ROOT__.'/Public/Admin/js',
        '__ADMIN_IMAGES__'   => OSS_URL.'/Public/Admin/images',
    ),
    
);

//【表单令牌验证】
$token = array(
    'TOKEN_ON'      =>    false,  // 是否开启令牌验证 默认关闭
    'TOKEN_NAME'    =>    '__hash__',    // 令牌验证的表单隐藏字段名称，默认为__hash__
    'TOKEN_TYPE'    =>    'md5',  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'   =>    true,  //令牌验证出错后是否重置令牌 默认为true
);

//【日志设置】
$logger = array(
    'LOG_RECORD'            => true,   // 默认不记录日志
    'LOG_FILE_SIZE'         => 2097152, // 日志文件大小限制
    'LOG_RECORD_LEVEL'      => 'INFO,EMERG,ALERT,CRIT,ERR',// 允许记录的日志级别
    'LOG_TYPE'              =>  'File', // 日志记录类型 默认为文件方式
);

//【邮件配置】
$mail = array(
    'MAIL_HOST'     => 'smtp.qq.com',          /*smtp服务器的名称、smtp.126.com*/
    'MAIL_SMTPAUTH' => TRUE,                    /*启用smtp认证*/
    'MAIL_DEBUG'    => TRUE,                    /*是否开启调试模式*/
    'MAIL_USERNAME' => '775743976@qq.com',      /*邮箱名称*/
    'MAIL_FROM'     => '775743976@qq.com',      /*发件人邮箱*/
    'MAIL_FROMNAME' => '小问询',                 /*发件人昵称*/
    'MAIL_PASSWORD' => 'fttiphwxvroobdeh',      /*发件人邮箱的密码*/
    'MAIL_CHARSET'  => 'utf-8',                 /*字符集*/
    'MAIL_ISHTML'   => TRUE,                    /*是否HTML格式邮件*/
    'MAIL_PORT'     => 465,                     /*邮箱服务器端口*/
    'MAIL_SECURE'   => 'ssl',                   /*smtp服务器的验证方式，注意：要开启PHP中的openssl扩展*/
    
    'EMAIL_FROM_NAME'        => '',   // 发件人
    'EMAIL_SMTP'             => '',   // smtp
    'EMAIL_USERNAME'         => '',   // 账号
    'EMAIL_PASSWORD'         => '',   // 密码  注意: 163和QQ邮箱是授权码；不是登录的密码
    'EMAIL_SMTP_SECURE'      => '',   // 链接方式 如果使用QQ邮箱；需要把此项改为  ssl
    'EMAIL_PORT'             => '25', // 端口 如果使用QQ邮箱；需要把此项改为  465
);

//【系统缓存配置】
$cacheConfig = $configArr['CACHE_CONFIG'];
$cacheArr = explode('://:@', $cacheConfig);
$cacheType = strtolower($cacheArr[0]);
list($cacheHost, $cachePort, $cacheDb) = preg_split("/[:\/]/",$cacheArr[1]);

if($cacheType==='redis') {
    
    //Redis缓存配置
    $cache = array(
        'DATA_CACHE_PREFIX' =>$configArr['CKEY'] . "_",//缓存前缀
        'DATA_CACHE_TYPE'=>'Redis',//默认动态缓存为Redis
        'REDIS_RW_SEPARATE' => false, //Redis读写分离 true 开启
        'REDIS_HOST'=>$cacheHost, //redis服务器ip，多台用逗号隔开；读写分离开启时，第一台负责写，其它[随机]负责读；
        'REDIS_PORT'=>$cachePort,//端口号
        'REDIS_TIMEOUT'=>'300',//超时时间
        'REDIS_PERSISTENT'=>false,//是否长连接 false=短连接
        'REDIS_AUTH'=>'',//AUTH认证密码
        'DATA_CACHE_TIME'=>0,      // 数据缓存有效期 0表示永久缓存
        'REDIS_DBINDEX'=>$cacheDb,//指定Redis库,默认是0
    );
    
}else if($cacheType==='memcache') {
    
    //Memcache缓存配置
    $cache = array(
        'DATA_CACHE_PREFIX' =>$configArr['CKEY'] . "_",//缓存前缀
        'DATA_CACHE_TYPE'       => 'Memcache',
        'MEMCACHE_HOST'         => $cacheHost,
        'MEMCACHE_PORT'         => $cachePort,
        'DATA_CACHE_TIME'       => 0,      // 数据缓存有效期 0表示永久缓存
        //'MEMCACHE_HOST'         => 'tcp://127.0.0.1:11211',
    );
}else{
    //默认文件缓存(数据缓存设置)
    $cache = array(
        'DATA_CACHE_TIME'       =>  0,      // 数据缓存有效期 0表示永久缓存
        'DATA_CACHE_COMPRESS'   =>  false,   // 数据缓存是否压缩缓存
        'DATA_CACHE_CHECK'      =>  false,   // 数据缓存是否校验缓存
        'DATA_CACHE_PREFIX'     =>  '',     // 缓存前缀
        'DATA_CACHE_TYPE'       =>  'File',  // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
        'DATA_CACHE_PATH'       =>  TEMP_PATH,// 缓存路径设置 (仅对File方式缓存有效)
        'DATA_CACHE_KEY'        =>  '',	// 缓存文件KEY (仅对File方式缓存有效)
        'DATA_CACHE_SUBDIR'     =>  false,    // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
        'DATA_PATH_LEVEL'       =>  1,        // 子目录缓存级别
    );
}

//【阿里OSS存储】
$oss = array(
    'ALIOSS_CONFIG'          => array(
        'KEY_ID'             => '', // 阿里云oss key_id
        'KEY_SECRET'         => '', // 阿里云oss key_secret
        'END_POINT'          => '', // 阿里云oss endpoint
        'BUCKET'             => ''  // bucken 名称
    ),
);

return array_merge($db,$common,$token,$logger,$mail,$cache,$oss);
