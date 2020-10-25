<?php declare(strict_types = 1);
namespace msqphp\base\str;
/**
 * 检测字符串是否相等
 *
 * @func_name     equals
 *
 * @param  string $string 输入字符
 * @param  string $target  目标字符
 *
 * @return bool
 */
return function (string $string, string $target) : bool {
    return $string === $target && hash_equals($string, $target);
};