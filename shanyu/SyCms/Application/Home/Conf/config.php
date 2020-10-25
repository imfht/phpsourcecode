<?php

$config=array(

    /* 03-默认设定 */
    'DEFAULT_THEME'         =>  'Default', // 默认模板主题名称

    /* 09-模板引擎设置 */
    'TMPL_ACTION_ERROR'     =>  THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE'   =>  THINK_PATH.'Tpl/think_exception.tpl',// 异常页面的模板文件
    'TMPL_FILE_DEPR'        =>  '_', //模板文件CONTROLLER_NAME与ACTION_NAME之间的分割符

    // 11-Think模板引擎标签库相关设定
    'TAGLIB_BUILD_IN'       =>  'cx,Common\TagLib\Home', // 内置标签库名称(标签使用不必指定标签库名称),以逗号分隔 注意解析顺序
    'TAGLIB_PRE_LOAD'       =>  '',   // 需要额外加载的标签库(须指定标签库名称)，多个以逗号分隔 

    /* 12-URL设置 */
    'URL_CASE_INSENSITIVE'  =>  true,   // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             =>  2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    'URL_PATHINFO_DEPR'     =>  '/',    // PATHINFO模式下，各参数之间的分割符号
    'URL_HTML_SUFFIX'       =>  'html',  // URL伪静态后缀设置
    'URL_DENY_SUFFIX'       =>  'ico|png|gif|jpg', // URL禁止访问的后缀设置

    'URL_PARAMS_BIND'       =>  true, // URL变量绑定到Action方法参数
    'URL_PARAMS_BIND_TYPE'  =>  1, // URL变量绑定的类型 0 按变量名绑定 1 按变量顺序绑定

    'URL_PARAMS_FILTER'     =>  true, // URL变量绑定过滤
    'URL_PARAMS_FILTER_TYPE'=>  '', // URL变量绑定过滤方法 如果为空 调用DEFAULT_FILTER

    'URL_ROUTER_ON'         =>  true,   // 是否开启URL路由
    'URL_ROUTE_RULES'       =>  array(
        //招聘
        //'job$'=>'job/index',

        //栏目通用
        ':cname/:id\d'=>'show/index',
        ':cname/p/:p\d'=>'list/index',
    ), 
    'URL_MAP_RULES'         =>  array(
        //'aboutus' => 'list/index?cid=62',
    ), 


    /* 21-静态缓存 */
    'HTML_CACHE_ON'     =>    false, // 开启静态缓存
    'HTML_CACHE_TIME'   =>    0,   // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX'  =>    '.html', // 设置静态缓存文件后缀
    'HTML_CACHE_RULES'  =>     array(  
        'Index:index'  =>     array('{:action}', '3600'), 
        'List:index'  =>     array('{cname}', '3600'), 
        'Show:index'  =>     array('{cname}/{id}', '3600'), 
        
    ),// 定义静态缓存规则

);

$route_rule=D('Common/Urlmap')->getRouteRule();
if(!empty($route_rule)){
    $config['URL_MAP_RULES']=array_merge($config['URL_MAP_RULES'],$route_rule);
}

return $config;