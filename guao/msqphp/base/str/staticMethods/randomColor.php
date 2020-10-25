<?php declare(strict_types = 1);
namespace msqphp\base\str;
/**
 * 创建随机rgb颜色
 *
 * @func_name     randomColor
 *
 * @return string 颜色
 */
return function () : string {
    return '#'.substr(str_shuffle('0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF'), 0, 6);
};