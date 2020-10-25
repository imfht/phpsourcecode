<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // +----------------------------------------------------------------------
    // | 模板文件设置
    // +----------------------------------------------------------------------


    //表单生成器模板
    'full_builder_layout'  => APP_PATH . 'common/builder/full_layout.htm',

	
	//自动表单 前台wap列表页母模板
	'automodel_listpage'=>APP_PATH.'common/builder/listpage/layout.htm',

    //自动表单 会员中心列表页母模板
    'automodel_userlistpage'=>APP_PATH.'common/builder/listpage/user_layout.htm',
	
	//自动表单 前台wap详情展示页母模板
    'automodel_showpage'=>APP_PATH.'common/builder/showpage/layout.htm',

	//后台公共布局母模板
    'admin_style_layout'  => APP_PATH . 'admin/view/layout.htm',

    //前台公共布局母模板
    //'index_style_layout'  => APP_PATH . 'index/view/default/layout.htm',

	//会员中心公共布局母模板
    'member_style_layout'  => APP_PATH . 'member/view/default/layout.htm',
	
	//自动布局模板
	//'auto_tpl_base_layout'  => APP_PATH . 'member/view/default/layout.htm',

	//基础模板
    'site_defalut_html'  => APP_PATH . 'common/builder/default.htm',

    //简朴模板
    'simple_layout'  => APP_PATH . 'common/view/simple_layout.htm',

    'dispatch_success_tmpl'  => APP_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => APP_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
	
	// +----------------------------------------------------------------------
    // | 验证码设置
    // +----------------------------------------------------------------------
    'captcha' => [
        // 验证码密钥
        'seKey'    => 'fdsa3453fz',
        // 验证码图片高度
        'imageH'   => 34,
        // 验证码图片宽度
        'imageW'   => 130,
        // 验证码字体大小(px)
        'fontSize' => 18,
        // 验证码位数
        'length'   => 4,
    ],

    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 插件目录路径
    'plugin_path'        => ROOT_PATH. 'plugins/',
    // 数据包目录路径
    'packet_path'        => ROOT_PATH. 'packet/',
    // 文件上传路径
    'upload_path'        => ROOT_PATH . 'public' . DS . 'uploads',
    // 文件上传临时目录
    'upload_temp_path'   => ROOT_PATH . 'public' . DS . 'uploads' . DS . 'temp/',
	
	// 升级日志文件
	'client_upgrade_edition'        => RUNTIME_PATH . '/client_upgrade_edition.php',


    // 应用命名空间
    'app_namespace'          => 'app',
    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式状态
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => ['plugins' => ROOT_PATH. 'plugins/','QCloud_WeApp_SDK' => ROOT_PATH. 'vendor/weapp-sdk/lib/'],
    // 扩展函数文件
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT],
    // 用户密码加密方式,一旦设置好,就不能修改 可选1,2,3
    'md5_pwd_type' => 0,
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认PJAX 数据返回格式,可选json xml ...
    'default_pjax_return'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'index',
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => true,
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,
    // 全局请求缓存排除规则
    'request_cache_except'   => [],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'               => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'htm',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
        'taglib_pre_load'     =>    'app\common\taglib\Qb',
        //'taglib_build_in'    =>    'cx,app\common\taglib\Demo'
    ],

    // 视图输出字符串内容替换
    'view_replace_str'       => [],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'                    => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'test', //File 参数代表写日志 test 代表关闭日志,以节省磁盘空间
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => ['error','sql'],
		'max_files'	=> 10, //日志文件最多只会保留10个
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache'                  => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    //'paginate'               => [
    //    'type'      => 'bootstrap',
    //    'var_page'  => 'page',
    //    'list_rows' => 15,
    //],
	
	'paginate'               => [  
       'type'      => 'page\Page',//分页类  
       'var_page'  => 'page',  
       'list_rows' => 15,  
   ], 
];
