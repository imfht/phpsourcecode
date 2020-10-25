<?php

/**
 * 扩展函数库
 * @author 徐亚坤 hdyakun@sina.com
 */

/**
 * 转换字符串中的变量占位符为数组中的值
 * @param  string array
 * @return string
 */
function variable_replace($string, $array)
{
    foreach ($array as $k => $v) {
        $string = str_replace('{'.$k.'}', $v, $string);
    }
    return $string;
}

/**
 * 根据用户ID生成图像地址
 * @param  int
 * @return string
 */
function make_dir_by_userid($userid)
{
    $dir = substr(md5($userid), 0, 2);
    return $dir;
}

/**
 * 根据ID生成图像地址
 * @param  int
 * @return string
 */
function make_dir_by_id($id)
{
    $dir = substr(md5($id), 0, 2);
    return $dir;
}

/**
 * 判断是否是手机客户端
 * @return boolean
 */
function is_mobile($ignore = false)
{
    if ($ignore) {
        return false;
    }

    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

    if (preg_match('/playstation/i', $user_agent) OR preg_match('/ipad/i', $user_agent) OR preg_match('/ucweb/i', $user_agent)) {
        return false;
    }

    if (preg_match('/iemobile/i', $user_agent) OR preg_match('/mobile\ssafari/i', $user_agent) OR preg_match('/iphone\sos/i', $user_agent) OR preg_match('/android/i', $user_agent) OR preg_match('/symbian/i', $user_agent) OR preg_match('/series40/i', $user_agent)) {
        return true;
    }

    return false;
}

/**
 * 判断是否处于微信内置浏览器中
 * @return boolean
 */
function in_weixin()
{
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

    if (preg_match('/micromessenger/i', $user_agent)) {
        return true;
    }

    return false;
}

/**
 * 图片别名加载
 * @param  dynamic  mixed  配置文件中的别名
 * @return string
 */
function img()
{
    $configKey = 'extend.imgAliases';

    $args = array_merge(array($configKey), func_get_args());
    return call_user_func_array('Html::img', $args);
}

/**
 * 样式别名加载
 * @param  dynamic  mixed  配置文件中的别名
 * @return string
 */
function style()
{
    $configKey = 'extend.cssAliases';

    $args = array_merge(array($configKey), func_get_args());
    return call_user_func_array('Html::css', $args);
}

/**
 * 脚本别名加载
 * @param  dynamic  mixed  配置文件中的别名
 * @return string
 */
function script()
{
    $configKey = 'extend.jsAliases';

    $args = array_merge(array($configKey), func_get_args());
    return call_user_func_array('Html::js', $args);
}