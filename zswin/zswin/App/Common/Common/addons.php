<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2020 http://zswin.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: www.zswin.cn
// +----------------------------------------------------------------------

/**
 * 处理插件钩子
 * @param string $hook 钩子名称
 * @param mixed  $params 传入参数
 * @return void
 */
function hook($hook, $params = array())
{
	
    \Think\Hook::listen($hook, $params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name)
{
    $class = "Addons\\{$name}\\{$name}Addon";
    return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_addon_config($name)
{
    $class = get_addon_class($name);
    if (class_exists($class)) {
        $addon = new $class();
        return $addon->getConfig();
    } else {
        return array();
    }
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array  $param 参数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function addons_url($url, $param = array())
{
    $url = parse_url($url);
    $case = C('URL_CASE_INSENSITIVE');
    $addons = $case ? parse_name($url['scheme']) : $url['scheme'];
    $controller = $case ? parse_name($url['host']) : $url['host'];
    $action = trim($case ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参数 */
    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    /* 基础参数 */
    $params = array(
        '_addons' => $addons,
        '_controller' => $controller,
        '_action' => $action,
    );
    $params = array_merge($params, $param); //添加额外参数
    if(strtolower(MODULE_NAME)=='admin'){
        return U('Admin/Addons/execute', $params);
    }else{
        return U('Home/Addons/execute', $params);

    }
}