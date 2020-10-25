<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------
use think\facade\Env;

return [
    // 应用名称
    'app_name'               => '',
    // 应用地址
    'app_host'               => '',
    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'Asia/Shanghai',
    // 是否开启多语言
    'lang_switch_on'         => true,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    // 默认语言
    'default_lang'           => 'en-us',
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
    // 默认的空模块名
    'empty_module'           => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法前缀
    'use_action_prefix'      => false,
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => true,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // HTTPS代理标识
    'https_agent_name'       => '',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由延迟解析
    'url_lazy_route'         => false,
    // 是否强制使用路由
    'url_route_must'         => false,
    // 合并路由规则
    'route_rule_merge'       => false,
    // 路由是否完全匹配
    'route_complete_match'   => false,
    // 使用注解路由
    'route_annotation'       => true,
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

    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => Env::get('think_path') . 'tpl/dispatch_jump.tpl',
    'dispatch_error_tmpl'    => Env::get('think_path') . 'tpl/dispatch_jump.tpl',

    // 异常页面的模板文件
    'exception_tmpl'         => Env::get('think_path') . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',
    //邮件发送
    'THINK_EMAIL'=>[
        'SMTP_HOST'=>'smtp.163.com',
        'SMTP_PORT'=>994,
        'SMTP_USER'=>'jswei30@163.com',
        'SMTP_PASS'=>'jswei30',
        'FROM_EMAIL'=>'jswei30@163.com',
        'FROM_NAME'=>'官方邮件',
        'REPLY_EMAIL'=>'',
        'REPLY_NAME'=>''
    ],
    //云之讯短信接口
    'Ucpaas'=>[
        'accountSid'=>'73ba580fb6aae884362e1ac7c9fc46b2',
        'authToken'=>'b7e16a1d2fe0cf267b0803f82813d621',
        'appId'=>'7ea4faede77749e995fd9ed9abe4d132',
    ],
    'AMAP'=>[
        'KEY'=>'5c34d8399b8beffa18d9b98731385bf3',
        'SECRET'=>'b2a743e44f477fef25fca9596f193402'
    ],
    'GeTui'=>[
        'AppID'=>'8lrYkxNeKS7Es2yg4QAFF8',
        'AppSecret'=>'u6yYf21r7mAzWvqM9I5rFA',
        'AppKey'=>'rjw33LZqHv84mkK3ojx4R6',
        'MasterSecret'=>'dPSlFDgKmB8FOoLOLNfGA1'
    ],
    'UPLOAD'=>[
        'UPLOAD_PATH'=>  './uploads',
        'UPLOAD_IMAGE'=>[
            'size'=>1024*1024*5,        //5M最大
            'ext'=>'jpg,jpeg,png,gif,bmp,webp'
        ],
        'UPLOAD_FILE'=>[
            'size'=>1024*1024*8,        //8M最大
            'ext'=>'txt,zip,tar,xls,pdf,doc,docx,rar,xlsx'
        ],
        'UPLOAD_EDITOR'=>[
            'size'=>1024*1024*8,        //8M最大
            'ext'=>'jpg,jpeg,png,gif,txt,zip,rar,tar,xls,pdf,doc,docx,xlsx'
        ],
    ]
];
