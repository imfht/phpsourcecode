<?php
/*
 * 2016年11月4日 星期五
 * 扩展配置文件/使用非频繁得全局配置文件
 * Joshua Conero
 */
return [
    'uSessin_key'   => 'conero_auth',               // * 获取登录session键值-user loing session key
    'vst_session'   => 'doeeking_vst_count',		// * 用户访问session 键值
    'theme_session' => 'doeeking_theme_count',		// * 网站主题默认 session 键值
    'theme_default' => 'bootstrap',		            //网站首页默认风格
    'lisa_ieads_file'=> 'Files/__cache/',           // Lisa 页面iead生成器
    'cache_dir'     => 'Files/__cache/',           // 缓存页面
    'avoid_crawler_max'=> 15,                       // 可访问最大限度， 15
    'vst_uid'       => 'Conero'                     // * session id前缀值 
];
