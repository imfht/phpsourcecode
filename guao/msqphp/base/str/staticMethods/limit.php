<?php declare(strict_types = 1);
namespace msqphp\base\str;
/**
 * 限定多宽的字符
 *
 * @func_name          limit
 *
 * @param  string      $string 字符串
 * @param  int|integer $limit  限制长度
 * @param  string      $end    结尾字符
 *
 * @return string
 */
return function (string $string, int $limit = 100, string $end = '...') : string {
    if (mb_strwidth($string, 'UTF-8') <= $limit) {
        return $string;
    }
    return rtrim(mb_strimwidth($string, 0, $limit, '', 'UTF-8')).$end;
};