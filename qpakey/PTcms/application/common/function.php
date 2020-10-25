<?php
/**
 * @Author: 杰少Pakey
 * @Email : admin@ptcms.com
 * @File  : common.php
 */

/**
 * 取子id
 *
 * @param $id
 * @return float
 */
function subid($id) {
    return floor($id / 1000);
}

/**
 * 文件大小格式化
 * @param integer $size 初始文件大小，单位为byte
 * @return array 格式化后的文件大小和单位数组，单位为byte、KB、MB、GB、TB
 */
function file_size_format($size = 0, $dec = 2) {
    $unit = array("B", "KB", "MB", "GB", "TB", "PB");
    $pos = 0;
    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }
    $result['size'] = round($size, $dec);
    $result['unit'] = $unit[$pos];
    return $result['size'].$result['unit'];
}


/**
 * 字符串截取，支持中文和其他编码
 *
 * @param string $string 需要转换的字符串
 * @param string $length 截取长度
 * @param string $suffix 截断显示字符
 * @param int $start     开始位置
 * @return string
 */
function truncate($string, $length, $suffix = '', $start = 0) {
    if (empty($string) or empty($length) or strlen($string) < $length) return $string;
    if (function_exists('mb_substr')) {
        $slice = mb_substr($string, $start, $length, 'utf-8');
    } elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($string, $start, $length, 'utf-8');
    } else {
        preg_match_all('/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/', $string, $match);
        $slice = implode('', array_slice(reset($match), $start, $length));
    }
    return $slice . $suffix;
}
