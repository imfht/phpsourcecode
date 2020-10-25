<?php

/**
 * 数组函数库
 * @author 徐亚坤 hdyakun@sina.com
 */

/**
 * 计算数组深度
 * @author 徐亚坤
 * @param array $array
 * @return int
 */
if (!function_exists('array_depth')) {
    function array_depth($array)
    {
        $max_depth = 1;
        foreach ($array as $value) {
            if (is_array($value)) {
                $max_depth = array_depth($value) + 1;
            }
        }
        return $max_depth;
    }
}

/**
 * 将多维数组转为一维数组
 * @author 徐亚坤
 * @param array $arr
 * @return array
 */
if (!function_exists('array_tile')) {
    function array_tile($arr)
    {
        // 将数值第一元素作为容器，作地址赋值。
        $ar_room =& $arr[key($arr)];
        // 第一容器不是数组进去转
        if (!is_array($ar_room)) {
            // 转为成数组
            $ar_room = array($ar_room);
        }
        // 指针下移
        next($arr);
        // 遍历
        while (list($k, $v) = each($arr)) {
            // 是数组就递归深挖，不是就转成数组
            $v = is_array($v) ? call_user_func(__FUNCTION__, $v) : array($v);
            // 递归合并
            $ar_room = array_merge_recursive($ar_room, $v);
            // 释放当前下标的数组元素
            unset($arr[$k]);
        }
        return $ar_room;
    }
}

/**
 * 兼容不支持 array_column 函数的 PHP 版本
 */
if (!function_exists('array_column')) {
    function array_column(array $array, $column_key, $index_key = null)
    {
        $result = [];
        foreach ($array as $arr) {
            if (!is_array($arr)) {
                continue;
            }

            $value = is_null($column_key) ? $arr : $arr[$column_key];

            if (!is_null($index_key)) {
                $key = $arr[$index_key];
                $result[$key] = $value;
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }
}

/**
 * 对象转数组, 使用 get_object_vars 返回对象属性组成的数组
 */
if (!function_exists('objectToArray')) {
    function objectToArray($obj)
    {
        $arr = is_object($obj) ? get_object_vars($obj) : $obj;
        // $arr = is_object($obj) ? (array) $obj : $obj;
        if (is_array($arr)) {
            return array_map(__FUNCTION__, $arr);
        } else {
            return $arr;
        }
    }
}

/**
 * 数组转对象
 */
if (!function_exists('arrayToObject')) {
    function arrayToObject($arr)
    {
        if (is_array($arr)) {
            return (object)array_map(__FUNCTION__, $arr);
        } else {
            return $arr;
        }
    }
}

/**
 * 获取数组最后一个元素
 */
if (!function_exists('last')) {
    function last($array)
    {
        return end($array);
    }
}

if (!function_exists('array_set')) {
    function array_set(&$array, $key, $value)
    {
        if (is_null($key)) return $array = $value;

        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = array();
            }
            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;
        return $array;
    }
}

if (!function_exists('array_get')) {
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) return $array;
        if (isset($array[$key])) return $array[$key];

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}

if (!function_exists('array_forget')) {
    function array_forget(&$array, $keys)
    {
        foreach ((array)$keys as $key) {

            $parts = explode('.', $key);
            while (count($parts) > 1) {
                $part = array_shift($parts);
                if (isset($array[$part]) && is_array($array[$part])) {
                    $array =& $array[$part];
                }
            }

            unset($array[array_shift($parts)]);
        }
    }
}

//二维数组排序
function array_sort($arr, $keys, $type = SORT_DESC)
{
    $keysvalue = $new_array = array();
    foreach ($arr as $k => $v) {
        $keysvalue[$k] = $v[$keys];
    }
    if ($type == SORT_ASC) {
        asort($keysvalue);
    } elseif ($type == SORT_DESC) {
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k => $v) {
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}

function array_remove_empty($arr)
{
    $narr = array();
    while (list($key, $val) = each($arr)) {
        if (is_array($val)) {
            $val = array_remove_empty($val);
            // does the result array contain anything?
            if (count($val) != 0) {
                // yes :-)
                $narr[$key] = $val;
            }
        } else {
            if (trim($val) != "") {
                $narr[$key] = $val;
            }
        }
    }
    unset($arr);
    return $narr;
}