<?php declare (strict_types = 1);
namespace msqphp\base\arr;

/**
 * 获取随机获取数组中固定个值
 *
 * @func_name random
 *
 * @param  array $array    随机数组
 * @param  array $count    随机个数(小于1取1)
 *
 * @return miexd
 */
return function (array $array, int $count = 1) {
    // 小于1,取1
    $count < 1 && $count = 1;
    // 取最小
    $count = min($count, count($array));

    if ($count === 1) {
        return $array[array_rand($array, 1)];
    } else {

        // 随机获取键
        $index = array_rand($array, $count);

        return array_filter($array, function ($key) use ($index) {
            return in_array($key, $index);
        }, ARRAY_FILTER_USE_KEY);
    }
};
