<?php
/**
 * 内置函数库文件
 * @author 暮雨秋晨
 * @copyright 2014
 */

/**
 * 获取文件后缀名
 */
function getExt($file)
{
    return pathinfo($file, PATHINFO_EXTENSION);
}

/**
 * 时间格式化
 */
function dateFormat($timestamp)
{
    $time = (time() - $timestamp) + 0;
    if ($time < 60) {
        return $time . '秒前';
    } elseif ($time < 3600) {
        return date('i分钟前', $time);
    } elseif ($time < 86400) {
        return date('H小时前', $time);
    } elseif ($time < 604800) {
        //如果小于七天
        return date('d天前', $time);
    } else {
        //大于七天直接返回时间
        return date('Y年m月d日', $timestamp);
    }
}

/**
 * 字符串截取（支持中英文混排）
 */
function strCut($str, $start = 0, $end = 1)
{
    if (empty($str)) {
        return false;
    }
    if (function_exists('mb_substr')) {
        if (func_num_args() >= 3) {
            $end = func_get_arg(2);
            return mb_substr($str, $start, $end, 'utf-8');
        } else {
            mb_internal_encoding("UTF-8");
            return mb_substr($str, $start);
        }
    } else {
        $null = "";
        preg_match_all("/./u", $str, $ar);
        if (func_num_args() >= 3) {
            $end = func_get_arg(2);
            return join($null, array_slice($ar[0], $start, $end));
        } else {
            return join($null, array_slice($ar[0], $start));
        }
    }
}

/**
 * 数据转义
 */
function DataEscape($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $data[$key] = DataEscape($val);
            } else {
                $data[$key] = htmlspecialchars(addslashes($val), ENT_QUOTES);
            }
        }
    } else {
        $data = htmlspecialchars(addslashes($data), ENT_QUOTES);
    }
    return $data;
}

/**
 * 变量信息打印函数
 */
function dump()
{
    $args = func_get_args();
    if (!empty($args)) {
        foreach ($args as $arg) {
            echo ("\r\n" . '<br />--[Debug start]--<br />' . "\r\n");
            if (is_array($arg)) {
                echo ('<pre>');
                print_r($arg);
                echo ('</pre>');
            } elseif (is_string($arg)) {
                echo ($arg);
            } else {
                var_dump($arg);
            }
            echo ("\r\n" . '<br />--[Debug   end]--<br />' . "\r\n");
        }
    }
}

/**
 * 生成URL
 * @param string $controller 控制器
 * @param string $action 操作
 * @param array  $params 参数
 */
function U($controller, $action, $params = array())
{
    return Router::get($controller, $action, $params);
}

/**
 * 获取model对象
 * @param string $table 数据表
 */
function M($table)
{
    return Model::getInstance($table);
}
