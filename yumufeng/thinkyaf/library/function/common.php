<?php
/**
 * Date: 2018\2\17 0017 23:36
 */
/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
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

/**
 * 清空目录
 * @param string $dir [存储目录]
 */
function clean_dir($dir)
{
    if (!is_dir($dir)) {
        return true;
    }
    $files = scandir($dir);
    unset($files[0], $files[1]);
    $result = 0;
    foreach ($files as &$f) {
        $result += @unlink($dir . $f);
    }
    unset($files);
    return $result;
}

/**
 * 输出一个人字符串 多少位 长度；
 **/
function str_strlen($str)
{
    $i = 0;
    $count = 0;
    $len = strlen($str);
    while ($i < $len) {
        $chr = ord($str[$i]);
        $count++;
        $i++;
        if ($i >= $len) break;
        if ($chr & 0x80) {
            $chr <<= 1;
            while ($chr & 0x80) {
                $i++;
                $chr <<= 1;
            }
        }
    }
    return $count;
}


/**
 * 记录日志
 * @param $content
 * @param string $filename
 * @param string $Separator
 * @return bool|int
 */
function logs($content, $filename = '', $sdir = '', $Separator = ",")
{
    if (is_array($content)) {
        $content = var_export($content,true);
    }
    $dir = Config::get('log_path');
    if (!is_dir($dir)) {
        @mkdir($dir, 0777);
    }
    if (!empty($sdir)) {
        $dir = $dir . '/' . $sdir;
        if (!is_dir($dir)) {
            @mkdir($dir, 0777);
        }
    }
    if (empty($filename)) {
        $filename = date('Y_m_d', time());
    }
    $result = file_put_contents($dir . '/' . $filename . '.log', (date('Y-m-d h:i:s', time())) . ' ： ' . $content . "\r\n", FILE_APPEND | LOCK_EX);
    return $result;
}