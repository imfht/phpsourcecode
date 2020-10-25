<?php
/**
 * VgotFaster Framework
 * 应用程序核心配置文件
 */

$config = array(
/**
 * 程序根目录访问地址
 *
 * 设置应用程序的访问地址,结尾请加上斜杠 "/"
 * 如果此处被设置为空,则程序会自动识别获取相对目录地址
 */
'base_url' => '',

/**
 * 框架接口文件地址
 *
 * 如果使用伪静态或者 QUERY_STRING 的方式路由
 * 如果使用伪静态,请将此项设置为空
 */
'index_file' => 'index.php',

/**
 * 默认控制器名称 Default Controller Name
 *
 * 此项必须设置,为 URI 缺省控制器时访问的默认控制器
 */
'default_controller' => 'welcome',

/**
 * 访问路由方式 PATH_INFO|GET|QUERY_STRING
 *
 * PATH_INFO:    welcome/index
 * GET:          ?ctrl=welcome&act=index
 * QUERY_STRING: ?welcome-index
 */
'router_method' => 'PATH_INFO',

/**
 * 访问路由GET方式名称
 *
 * 当路由方法为 GET 的时候的设置
 * controller 为控制器名称,action为动作名称,即控制器方法名称
 * Example: index.php?ctrl=welcome&act=index
 */
'router_get_params' => array(
	'controller' => 'ctr',
	'action'     => 'act'
),

/**
 * URI 分隔符
 *
 * 设定 URI 连接字符串，在三种路由模式下均有效果
 */
'uri_separator' => '/',

/**
 * 是否自动替换分隔符
 *
 * 是否将 URL 函数中输入的 / 转换成设定的分隔符
 */
'uri_separator_replace' => false,

/**
 * 访问页面后缀
 *
 * 您可以使用 .html 等，配合 Rewrite 规则，您可以伪造页面文件后缀
 * 注意：设置此处时请在后缀前带完整的点，如“.html”，而非“html”
 */
'url_suffix' => '',

/**
 * Encrypt 加解密密钥
 *
 * Encrypt 加密的字串有时效性和环境性，适用于用户登录
 */
'auth_key' => 'auth_key_123',

/**
 * Cookie 全局配置
 *
 * 用于相关,如 Session 等类与函数调用默认参数
 */
'cookie_prefix' => '',
'cookie_domain' => '',
'cookie_path'   => '/',

/**
 * Session 参数配置
 *
 * 配置 Session 类的动作及属性
 */
'session_cookie_name'     => 'vf_session_id', //通信 Cookie 名称
'session_expire'          => 2592000,         //有效活跃期，距最后一次活跃超过此时间的 Session 数据将被回收(秒)
'session_use_database'    => TRUE,            //是否使用数据库方式存储数据，默认是，现在仅有数据库存储方式
'session_db_table'        => 'vf_sessions',   //数据表名称，这里没有包括前缀，创建表时需要带有数据库配置中的前缀
'session_time_to_update'  => 3600,            //定期更新最后活跃时间以保持在有效期(秒)
'session_match_ip'        => TRUE,            //是否验证用户 IP 地址
'session_match_useragent' => TRUE,            //是否验证用户的 UserAgent,只取前 50 位字符

/**
 * 默认语言
 *
 * 系统自带的相关函数和类库中采用此默认的语言，选项也就是语言文件所在文件夹的名称
 */
'default_language' => 'zh-cn',


/**
 * 视图文件后缀
 *
 * 此选项不建议更改，建议保持默认的 php
 * 注意：此处后缀不要填写点符号 "."
 */
'view_file_extension' => 'php',

/**
 * 视图短标签支持
 *
 * 是否在系统不支持短标签时自动开启视图及模板短标签支持
 * 开启此项，即使 php.ini 配置中关闭短标签支持，您也可以在视图中使用 <?=?> 格式的短标签来输出内容
 */
'open_short_tag' => TRUE,

/**
 * 模板文件默认后缀
 * 模板输出是否清除空白行与缩进
 *
 * 注意：此处后缀不要填写点符号 "."
 */
'template_file_extension' => 'htm',
'template_clean_blank' => TRUE,

/**
 * URI 允许字符串正则表达式
 *
 * 默认正则 "/^[\w\d\|\/\-_~\.]+$/i" 允许多个的字母数字，符号 | / - _ ~ . 并且不区分大小写
 * 系统使用此方式减少可能来自URI的安全问题。考虑到安全问题，若不了解系统，请勿随意更改此处
 */
'uri_allowed_regular' => '/^[\w\d\|\/\-_~\.]+$/i',

/**
 * 是否开启 MAGIC_QUOTES_GPC 支持
 *
 * 同等于 php.ini 中的 magic_quotes_gpc 选项
 * 注意：此处的设置将会覆盖 php.ini 中的设置，即应用程序运行时，系统根据此处设置对相关全局变量处理
 */
'open_magic_quotes_gpc' => TRUE
);
