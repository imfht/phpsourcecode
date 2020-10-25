<?php

use think\Session;
use think\Config;
use think\Cache;
use think\Db;
use think\Cookie;

/**
 * 对象转为数组
 * @param object $object 数据对象
 * @author staitc7 <static7@qq.com>
 * @return array|null|object
 */
function object_to_array(&$object) {
    if (is_array($object)) {
        foreach ($object as $k => $v) {
            $object[$k] = $v->toArray();
        }
    }
    return $object ?? null;
}

/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data) {
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 从数组中取出索引项
 * @param type $arg 参数
 * @param array|type $list 数组
 * @return string
 */
function change_status($arg, $list = ['-1' => '删除', '0' => '禁用', '1' => '正常']) {
    if (array_key_exists($arg, $list)) {
        $value = $list[$arg];
    }
    return $value ?? '未知';
}

/**
 *  不区分大小写的in_array实现
 * @author staitc7 <static7@qq.com>
 * @param $value
 * @param $array
 * @return bool
 */
function in_array_case($value, $array) {
    return in_array(strtolower($value), array_map('strtolower', $array));
}

/**
 * 获取配置的类型
 * @param int|string $type 配置类型
 * @return string
 * @author staitc7 <static7@qq.com>
 */
function get_config_type($type = 0) {
    $list = Config::get('config_type_list');
    return $list[$type];
}

/**
 * 获取配置的分组
 * @param int|string $group 配置分组
 * @return string
 * @author staitc7 <static7@qq.com>
 */
function get_config_group($group = 0) {
    $list = Config::get('config_group_list');
    return $group ? $list[$group] : '';
}

/**
 * 系统加密方法
 * @param string $data_tmp 要加密的字符串
 * @param string $key_tmp 加密密钥
 * @param int $expire 过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data_tmp, $key_tmp = '', $expire = 0) {
    $key = md5(empty($key_tmp) ? Config::get('key.data_auth_key') : $key_tmp);
    $data = base64_encode($data_tmp);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }
        $char .= substr($key, $x, 1);
        $x++;
    }
    $str = sprintf('%010d', $expire ? $expire + time() : 0);
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($str));
}

/**
 * 时间戳格式化
 * @param int $time
 * @param string $format 时间格式
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = null, $format = 'Y-m-d H:i:s') {
    return (int)$time > 0 ? date($format, $time) : '';
}

/**
 * 字符串截取，支持中文和其他编码
 * @param string $str 需要转换的字符串
 * @param int|string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param bool|string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
    if (function_exists("mb_substr")) {
        $slice = mb_substr($str, $start, $length, $charset);
    } elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '...' : $slice;
}