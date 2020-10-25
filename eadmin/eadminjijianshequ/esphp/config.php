<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

return [

    'app_namespace'       => 'app',
    // 默认时区
    'default_timezone'    => 'PRC',
    'language'            => 'zh-cn', //语言
    'default_filter'      => 'stripslashes,htmlentities,htmlspecialchars,strip_tags',

    // 默认模块名
    'default_module'      => 'index',
    // 默认控制器名
    'default_controller'  => 'Index',
    // 默认操作名
    'default_action'      => 'index',
    // 默认输出类型
    'default_return_type' => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return' => 'json',
    // 默认验证器
    'default_validate'    => '',
    // URL伪静态后缀
    'url_html_suffix'     => 'html',
    // 表单请求类型伪装变量
    'var_method'          => '_method',
    // 表单ajax伪装变量
    'var_ajax'            => '_ajax',
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'      => 0,
    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'        => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'      => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'       => '/',
    'is_https'            => false,
    // HTTPS代理标识
    'https_agent_name'    => '',
    //分页配置
    'paginate'            => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
];