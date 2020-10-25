<?php
/**
 * UCenter客户端配置文件
 * 注意：该配置文件请使用常量方式定义
 */

define('UC_APP_ID', 1); //应用ID
define('UC_API_TYPE', 'Model'); //可选值 Model / Service
define('UC_AUTH_KEY', '[AUTH_KEY]'); //加密KEY
define('UC_DB_DSN', '[DB_TYPE]://[DB_USER]:[DB_PWD]@[DB_HOST]:[DB_PORT]/[DB_NAME]'); // 数据库连接，使用Model方式调用API必须配置此项
define('UC_TABLE_PREFIX', '[DB_PREFIX]'); // 数据表前缀，使用Model方式调用API必须配置此项
$view_conf=include './Application/Common/Conf/user_view_config.php';
$config = array(
    // 预先加载的标签库
    'TAGLIB_PRE_LOAD'     =>    'OT\\TagLib\\Article,OT\\TagLib\\Think,JYmusic\\TagLib\\Gq,JYmusic\\TagLib\\JY',
    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX' => 'jy_user_', // 缓存前缀 
        /* 文件上传相关配置 */
    'USER_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //下载模型上传配置（文件上传类配置）
	/* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'jy_home_', //session前缀
    'COOKIE_PREFIX'  => 'jy_home_', // Cookie前缀 避免冲突
    'VAR_SESSION_ID' => 'session_id',	//修复uploadify插件无法传递session_id的bug
    
	/* 模板相关配置 */
    'TMPL_FILE_DEPR'=>'_',
    
	//'TOKEN_ON'      =>    true,  // 是否开启令牌验证 默认关闭
    'DEFAULT_FILTER'        =>  'strip_tags,stripslashes', //过滤方法
    
	/* 错误页面模板 */
    'TMPL_ACTION_ERROR'     => './Public_error', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   => './Public_success', // 默认成功跳转对应的模板文件
);
if(is_array($view_conf)){
	$config  = array_merge($config,$view_conf);
}
return  $config;
