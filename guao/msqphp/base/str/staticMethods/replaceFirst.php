<?php declare(strict_types = 1);
namespace msqphp\base\str;
/**
 * 替换第一个出现的指定字符
 *
 * @func_name     replaceFirst
 *
 * @param  string $search  查找字符
 * @param  string $replace 替换字符
 * @param  string $subject 字符串
 *
 * @return string
 */
return function (string $search, string $replace, string $subject) : string {
    if (false !== $position = strpos($subject, $search)) {
        return substr_replace($subject, $replace, $position, strlen($search));
    }
    return $subject;
};