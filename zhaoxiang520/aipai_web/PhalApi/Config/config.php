<?php
/**
 * config.php
 * @since   2016-08-26
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

return [
    //全局基本设置
    'DEBUG' => True,
    'RETURN_TYPE' => 'json',  //json|xml 数据输出的格式

    //Cookie设置
    'COOKIE_EXPIRE'          => 0, // Cookie有效期
    'COOKIE_DOMAIN'          => '', // Cookie有效域名
    'COOKIE_PATH'            => '/', // Cookie路径
    'COOKIE_PREFIX'          => '', // Cookie前缀 避免冲突
    'COOKIE_SECURE'          => false, // Cookie安全传输
    'COOKIE_HTTPONLY'        => '', // Cookie httponly设置

    // 默认设定
    'DEFAULT_TIMEZONE'       => 'Asia/Shanghai',
    'DEFAULT_LANGUAGE'       => 'zh_cn',
    'DEFAULT_FILTER'         => 'htmlspecialchars', // 默认参数过滤方法
    'DEFAULT_MODULE'         => 'Demo',      //默认模块
    'DEFAULT_CLASS'          => 'Index',   //默认类
    'DEFAULT_ACTION'         => 'Index',     //默认方法

    // 数据库设置
    'DB_TYPE'                => '', // 数据库类型
    'DB_HOST'                => '', // 服务器地址
    'DB_NAME'                => '', // 数据库名
    'DB_USER'                => '', // 用户名
    'DB_PWD'                 => '', // 密码
    'DB_PORT'                => '', // 端口
    'DB_PREFIX'              => '', // 数据库表前缀
    'DB_PARAMS'              => [], // 数据库连接参数
    'DB_DEBUG'               => true, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'        => true, // 启用字段缓存
    'DB_CHARSET'             => 'utf8', // 数据库编码默认采用utf8

    // 数据缓存设置
    'DATA_CACHE_TYPE'        => 'file', // 数据缓存类型
    'DATA_CACHE_HOST'        => '127.0.0.1', //缓存服务地址
    'DATA_CACHE_PORT'        => 6379, // 缓存服务端口
    'DATA_CACHE_PASSWORD'    => '', // 缓存密码
    'DATA_CACHE_TIMEOUT'     => 0, // 缓存链接超时时间
    'DATA_CACHE_EXPIRE'      => 0, // 缓存过期时间
    'DATA_CACHE_PERSISTENT'  => false, // 是否强制链接
    'DATA_CACHE_PREFIX'      => '', // 键前缀
    'DATA_CACHE_CHECK'       => false, // 数据缓存是否校验缓存
    'DATA_CACHE_PATH'        => '', // 缓存路径设置 (仅对File方式缓存有效)
    'DATA_CACHE_KEY'         => '', // 缓存文件KEY (仅对File方式缓存有效)

    // 错误设置
    'ERROR_MESSAGE'          => '页面错误！请稍后再试～', //错误显示信息,非调试模式有效

    // 日志设置
    'LOG_TYPE'               => 'File', // 日志记录类型 默认为文件方式
    'LOG_PATH'               => '/Runtime/Logs', //文件日志保存的路径(只对文件类日志生效)

    // SESSION设置
    'SESSION_AUTO_START'     => true, // 是否自动开启Session
    'SESSION_OPTIONS'        => [], // session 配置数组 支持type name id path expire domain 等参数

    // URL设置
    'URL_HTML_SUFFIX'        => 'do', // URL伪静态后缀设置
    'URL_DENY_SUFFIX'        => 'ico|png|gif|jpg', // URL禁止访问的后缀设置
    'URL_TYPE'               => 0, //0:PathInfo 1:普通模式

    // 系统变量名称设置
    'CHECK_APP_DIR'          => true, // 是否检查应用目录是否创建
    'FILE_UPLOAD_TYPE'       => 'Local', // 文件上传方式
    'DATA_CRYPT_TYPE'        => 'Think', // 数据加密方式

];