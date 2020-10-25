<?php declare (strict_types = 1);
namespace msqphp\base\arr;

/**
 * 按键冒泡排序
 *
 * @func_name     bubbleSort
 *
 * @param  array  $arr 待排序数组
 *
 * @return array
 */
return function (array $arr): array{
    for ($i = 1, $len = count($arr); $i < $len; ++$i) {
        for ($k = 0; $k < $len - $i; ++$k) {
            $arr[$k] > $arr[$k + 1] && list($arr[$k], $arr[$k + 1]) = [$arr[$k + 1], $arr[$k]];
        }
    }
    return $arr;
};
