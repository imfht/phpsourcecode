<?php declare (strict_types = 1);
namespace msqphp\base\arr;

/**
 * 按键选择排序
 *
 * @func_name selectSort
 *
 * @param  array  $arr 待排序数组
 *
 * @return array
 */
return function (array $arr): array{
    for ($i = 0, $len = count($arr); $i < $len - 1; ++$i) {
        $p = $i;
        for ($j = $i + 1; $j < $len; ++$j) {
            $arr[$p] > $arr[$j] && $p = $j;
        }
        $p !== $i && list($arr[$p], $arr[$i]) = [$arr[$i], $arr[$p]];
    }
    return $arr;
};
