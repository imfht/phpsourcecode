<?php
return array(

    //常规设置
//    'TAGLIB_BUILD_IN'       =>  'Cx,Common\Tag\My',           //加载自定义标签
    'LOAD_EXT_CONFIG'       =>  'database,webconfig,oauth',     //加载网站设置文件
    'MODULE_ALLOW_LIST'     =>  array('Home','Admin','Api'),    //允许访问列表、

    /* 模板引擎设置 */
    'TMPL_CONTENT_TYPE'     =>  'text/html', // 默认模板输出类型
    'TMPL_DETECT_THEME'     =>  false,       // 自动侦测模板主题
    'TMPL_TEMPLATE_SUFFIX'  =>  '.html',     // 默认模板文件后缀
    'TMPL_PARSE_STRING'     =>  array(                        //定义常用路径
        '__HOME__'          =>  __ROOT__.'/Public/home',      //前端公共文件
        '__ADMIN__'         =>  __ROOT__.'/Public/admin',     //后台公共文件
        '__PLUGINS__'       =>  __ROOT__.'/Public/plugins',     //第三方插件
    ),

    //URL配置
    'URL_MODEL'             =>  2,                           // 为了兼容性更好而设置成1 如果确认服务器开启了mod_rewrite 请设置为 2
    'URL_CASE_INSENSITIVE'  =>  true,                        // 是否区分url大小写 为true不区分 必须遵守Think命名规范
    'URL_HTML_SUFFIX'        => '',                          // URL伪静态后缀设置
);
