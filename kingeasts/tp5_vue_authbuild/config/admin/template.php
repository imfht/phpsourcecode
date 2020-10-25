<?php
// +----------------------------------------------------------------------
// | TpAndVue
// +----------------------------------------------------------------------
// | Author: King east <1207877378@qq.com>
// +----------------------------------------------------------------------
$APP_PATH = empty(Env::get('APP_PATH')) ? Env::get('APP_PATH') : '/';
return [
    // 模板替换
    'tpl_replace_string'  =>  [
        '__ROOT__'     => $APP_PATH,
        '__BASE__'     => $APP_PATH . 'static/base',
        '__PUBLIC__'   => $APP_PATH,
        '__STATIC__'   => $APP_PATH . 'static',
        '__LIBS__'     => $APP_PATH . 'static/libs',
        '__ADMINCSS__' => $APP_PATH . 'static/admin/css',
        '__ADMINJS__'  => $APP_PATH . 'static/admin/js',
        '__ADMINIMG__' => $APP_PATH . 'static/admin/images',
        '__INDEXCSS__' => $APP_PATH . 'static/index/css',
        '__INDEXJS__'  => $APP_PATH . 'static/index/js',
        '__INDEXIMG__' => $APP_PATH . 'static/index/images',
    ],
];