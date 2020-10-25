<?php declare (strict_types = 1);
namespace msqphp\base\arr;

/**
 * 按键快速排序
 *
 * @func_name quickSort
 *
 * @param  array  $arr 待排序数组
 *
 * @return array
 */
return function (array $arr): array{
    // 数组长度
    $l = count($arr);

    if ($l <= 1) {
        return $arr;
    }

    $mid   = $arr[0];
    $left  = [];
    $right = [];

    for (--$l; $l > 0; --$l) {
        $mid > $arr[$l] && ($left[] = $arr[$l]) || ($right[] = $arr[$l]);
    }

    return array_merge(static::quickSort($left), [$mid], static::quickSort($right));
};
