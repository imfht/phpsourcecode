<?php
/**
 * Some useful extend tools for PHP
 */
namespace zendforum\Phplus;

/**
 * for Array
 */
class Array_ {

    /**
     * 获取数组的维度
     * @param array $array
     * @return int
     */
    public static function dimension ($array = []) {
        if(!is_array($array)) return 0;

        $max_depth = 1;
        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = self::dimension($value) + 1;
                $max_depth = max($max_depth, $depth);
            }
        }

        return $max_depth;
    }

    /**
     * 获取数组的维度
     * @param array $array
     * @return int
     */
    public static function dimension_ ($array = []) {
        if(!is_array($array)) return 0;
        $al = [0];

        $ad = function ($array, &$al, $level = 0) use (&$ad) {
            if (is_array($array)) {
                $level++;
                $al[] = $level;
                foreach ($array as $v) {
                    $ad ($v, $al, $level);
                }
            }
        };
        $ad($array, $al);

        return max($al);
    }

    /**
     * 数组生成器,生成任何维度的数组;简单的一维数组生成直接使用函数:range()
     * @param int $count 单元数
     * @param mixed $padding 填充物
     * @param int $dimension 维度
     * @return array
     */
    public static function generator ($count = 1, $padding = 1, $dimension = 1) {
        if (!Int_::is_id($count) || !Int_::is_id($dimension)) return [];

        //php限制,一次最多填补1048576个单元。详情:http://www.php.net/manual/zh/function.array-pad.php
        if ($count > 1048576) $count = 1048576;

        $arr = [];
        if ($dimension == 1) {
            return array_pad($arr, $count, $padding);
        }
        else {
            return array_pad($arr, $count, self::generator($count, $padding, $dimension - 1));
        }
    }

}
