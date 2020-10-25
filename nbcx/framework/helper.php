<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * 格式打印
 * @param 不定变量
 */
function e() {
    call_user_func_array('\nb\Debug::e',func_get_args());
}

/**
 * 浏览器友好的变量输出
 * @access public
 * @param  mixed         $var 变量
 * @param  boolean       $detailed 是否详细输出 默认为true 如果为false 则使用print_r输出
 * @param  string        $label 标签 默认为空
 * @param  integer       $flags htmlspecialchars flags
 * @return void|string
 */
function ex($var, $detailed = false, $label = null, $flags = ENT_SUBSTITUTE) {
    call_user_func_array('\nb\Debug::ex',func_get_args());
}

/**
 * 格式打印并结束运行
 */
function ed($var = null) {
    call_user_func_array('\nb\Debug::ed',func_get_args());
}

/**
 * 格式打印加强版
 * 同时输出函数调用路径信息
 */
function ee($var) {
    call_user_func_array('\nb\Debug::ee',func_get_args());
}

/**
 * 将一个数组写入一个php文件里
 * @param $data
 * @param $fileName 不带后戳的文件名,根路径为path_temp所指向的路径
 * @return int
 */
function efile($data, $fileName) {
    return \nb\Cache::php($data,$fileName);
    /*
    $filePath = nb\Config::getx('path_temp') . $fileName.'.php';
    $result = file_put_contents($filePath, "<?php\nreturn " . var_export($data, true) . ";");
    return $result;
    */
}



/**
 * 向debug页面添加需要显示的变量
 * @param object $k
 * @param object $v
 */
function b($k, $v = null) {
    nb\Debug::record(1,$k, $v);
}

/**
 * 记录信息到日志文件
 * 底层是通过error_log函数
 * @param unknown $data
 * @param string $fileName
 */
function l($data, $fileName = 'log', $ext='txt', $format='Ymd') {
    \nb\Debug::log($data, $fileName, $ext, $format);
}

/**
 * 代替原生的die函数,方便程序在不同的环境下运行
 * @param null $msg
 * @throws Exception
 */
function quit($msg=null) {
    \nb\Debug::quit($msg);
}

function val() {
    return call_user_func_array(
        ['\nb\Pool','value'],
        func_get_args()
    );
}

function obj($alias,$namespace=null,$args=[]) {
    return \nb\Pool::object($alias,$namespace,$args);
}

/**
 * @param null $tableName
 * @param string $id
 * @param string $server
 * @return \nb\Dao
 */
function dao($tableName=null,$id='id',$server = 'dao') {
    return \nb\Pool::object("\\nb\\Dao:{$tableName}",'\nb\Dao',[
        $tableName,
        $id,
        $server
    ]);
}

/**
 * 返回模版类型为PHP的视图完整路径
 * PATH_TEMPLATES.$tbl.".{$ex}"
 * @param String $tbl 要显示的模板
 */
function view($tbl, $ex = 'php') {
    $module = \nb\Router::driver()->module;
    if($module) {
        $path = __APP__.'module'.DS.$module.DS;
    }
    else {
        $path = nb\Config::$o->app.DS;
    }
    return $path.'view'.DS . $tbl . ".{$ex}";
}


function template($tbl='', $config = []) {
    $tpl = new \nb\Template($config);
    return $tpl->path($tbl);
}

/**
 * 获取路由对象或指定的路由属性值
 * @param null $name
 * @return $this
 */
function router($name=null) {
    if($name === null) {
        return \nb\Router::driver();
    }
    return \nb\Router::get($name);
}

/**
 * 生成一个反解析的URL地址
 * @param $name
 * @param array|null $value
 * @param null $prefix
 * @return mixed
 */
function url($name, array $value = null, $prefix = null) {
    return \nb\Router::url($name, $value, $prefix);
}

/**
 * 调用框架内置的提示页面
 * @param $hint 提示标题
 * @param $message 提示信息
 * @param $url 提示完毕后要跳转的地址
 * @param $wait 提示时长
 */
function tips($hint,$message,$url=null,$wait=3){
    \nb\Pool::object('nb\\event\\Framework')->tips(
        $hint,
        $message,
        $url,
        $wait
    );
}

/**
 * 读取PATH_AUTOINCLUDE路径下的配置文件类容
 * @param String $fileName 文件名字，不需要带ini.php后撤
 * @param Boolean $return ture 返回一个数组对象，false包含文件
 */
function conf($k, $v = null) {
    $value =  nb\Config::get($k);
    if ($value) {
        if ($v === null) {
            return $value;
        }
        nb\Config::set($k,$v);
    }
    return $v;
}

/**
 * Cookie 设置、获取、删除
 * @param string $name cookie名称
 * @param mixed $value cookie值
 * @param mixed $option cookie参数
 * @return mixed
 */
function cookie($name = '', $value = null, $option = null) {
    if($value) {
        \nb\Cookie::set($name,$value,$option);
    }
    else {
        return \nb\Cookie::get($name);
    }
}

/**
 * session管理函数
 * @param string|array $name session名称 如果为数组则表示进行session设置
 * @param mixed $value session值
 * @return mixed
 */
function session($name = '', $value = null) {
    if($value) {
        \nb\Session::set($name,$value);
    }
    else {
        return \nb\Session::get($name);
    }
}

/**
 * 快速获取一个redis对象
 * @param $server
 * @return \nb\utility\Redis
 */
function redis($server) {
    return \nb\utility\Redis::instance($server);
}


/**
 * I18n function
 *
 * @param string $string 需要翻译的文字
 * @return string
 */
function t($string, $vars = []) {
    return \nb\I18n::t($string, $vars);

    /*
    if (func_num_args() <= 1) {
        return nb\I18n::translate($string);
    }
    else {
        $args = func_get_args();
        array_shift($args);
        return vsprintf(nb\I18n::translate($string), $args);
    }
    */
}

/**
 * I18n function
 * 针对复数形式的翻译函数
 *
 * @param string $single 单数形式的翻译
 * @param string $plural 复数形式的翻译
 * @param integer $number 数字
 * @return string
 */
function _n($single, $plural, $number) {
    return str_replace('%d', $number, nb\I18n::driver()->ngettext($single, $plural, $number));
}

/**
 * 有些不常用的文件,为了性能,不加入自动加载中
 * 可以通过此函数,手动加载
 * 此函数自动从path_autoinclude指定的路径里面去寻找
 *
 * @param $file 文件名,
 * @param string $ext 文件后戳名
 */
function load($file,$ext = '.inc'){
    $path = \nb\Config::$o->getx('path_autoinclude');
    $sp = explode('@',$file);
    if(isset($sp[1])) {
        $path = $path[$file[0]];
        $file = $path.$sp[1].$ext;
        if(is_file($file)) {
            return include $file;
        }
    }
    else {
        $file .= $ext;
        foreach($path as $v) {
            if(is_file($v.$file)) {
                return include $v.$file;
            }
        }
    }
    return null;
}

function tplreplace($url) {
    $tpl = \nb\Config::getx('templates');
    $tpl = $tpl['tpl_replace_string'];
    $find = array_keys($tpl);
    $replace = array_values($tpl);
    return str_replace($find,$replace,$url);
}

/**
 * 重定向跳转
 * @param $url
 * @param int $http_response_code
 */
function redirect($url='/', $http_response_code=302) {
    \nb\Response::redirect($url,$http_response_code);
}