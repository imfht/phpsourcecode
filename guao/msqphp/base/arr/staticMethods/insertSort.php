<?php declare (strict_types = 1);
namespace msqphp\base\arr;

/**
 * 按键插入排序
 *
 * @func_name     insertSort
 *
 * @param  array  $arr 待排序数组
 *
 * @return array
 */
return function (array $arr): array{
    for ($i = 1, $len = count($arr); $i < $len; ++$i) {
        $tmp = $arr[$i];
        for ($j = $i - 1; $j >= 0; --$j) {
            $tmp < $arr[$j] && list($arr[$j + 1], $arr[$j]) = [$arr[$j], $tmp];
        }
    }
    return $arr;
};
