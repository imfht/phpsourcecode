<?php

mb_internal_encoding('UTF-8');

// 变量调试
function p($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

// 获取已经安装的模块
function get_app($name=''){
    static $apps;
    if (!$apps && !$apps = \think\Cache::get('eb_apps')) {
        $apps = \think\Db::name('app') -> where('status',1) -> column(true,'name');
        \think\Cache::set('eb_apps',$apps);
    }
    if ($name) {
        return isset($apps[$name])?$apps[$name]:false;
    }else{
        return $apps;
    }
}
// 判断是否安装app
function check_app($name=''){
    $name = strtolower($name);
    // 排除三个内置模块
    $not_check = ['admin','index'];
    if (in_array($name, $not_check)) {
        return true;
    }
    if ($name && $app = get_app($name)) {
        if ($app['status']) {
            return true;
        }
    }
    return false;
}

function is_login(){
    return \think\Session::get('user_id') ? true : false;
}

function user($field=null){
    static $user='';
    if ('' === $user) {
        $user = \app\user\model\User::get(\think\Session::get('user_id')?:0);
    }
    return $field?$user[$field]:$user;
}

function get_root($domain = false)
{
    $str = dirname(request()->baseFile());
    $str = ($str == DS) ? '' : $str;
    return $domain ? request()->domain() . $str : $str;
}

// 检查权限
function check_auth($action, $controller = '', $module = ''){
    if (\think\Session::get('super_admin')) {
        return true;
    }
    static $auth;
    if (!$auth) {
        $prefix = \think\Config::get('database.prefix');
        $config = [
            'AUTH_GROUP' => $prefix . 'auth_group',
            'AUTH_ACCESS' => $prefix . 'auth_access',
            'AUTH_RULE' => $prefix . 'auth_rule',
            'AUTH_USER' => $prefix . 'manager',
            'AUTH_ON' => true,
            'AUTH_TYPE' => \ebcms\Config::get('system.base.auth_type'),
        ];
        $auth = new \ebcms\Auth($config);
    }
    $module = $module ?: request()->module();
    $controller = $controller ?: request()->controller();
    $action = $action ?: request()->action();
    $node = strtolower($module . '_' . $controller . '_' . $action);
    if ($auth->check($node, \think\Session::get('manager_id'))) {
        return true;
    }
    return false;
}

function eb_config($name)
{
    return \ebcms\Config::get($name);
}

// 获取缩略图真实地址
function thumb($file, $width = 0, $height = 0, $type = 3)
{
    if (strpos($file, '://')) {
        return $file;
    }
    $base = request() -> root();
    if (!$width || !$height) {
        if (is_file('./upload' . $file)) {
            return $base . '/upload' . $file;
        }
        return $base . '/system/image/nopic.gif';
    } else {
        $res = $base . '/upload' . $file . '!' . $width . '_' . $height . '_' . $type . '.' . pathinfo($file, PATHINFO_EXTENSION);
        $thumbfile = './upload' . $file . '!' . $width . '_' . $height . '_' . $type . '.' . pathinfo($file, PATHINFO_EXTENSION);
        $file = './upload' . $file;
    }
    if (!is_file($thumbfile)) {
        if (!is_file($file)) {
            return $base . '/system/image/nopic.gif';
        } else {
            \think\Image::open($file)->thumb($width, $height, $type)->save($thumbfile, null, 100);
        }
    }
    return $res;
}

// 获取缩略图真实地址
function file_url($file)
{
    return strpos($file, '://') ? $file : request() -> root() . '/upload' . $file;
}