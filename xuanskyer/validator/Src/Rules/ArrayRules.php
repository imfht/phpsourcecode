<?php
/**
 * Desc: 数组格式化|验证规则
 * Created by PhpStorm.
 * User: xuanskyer | <furthestworld@icloud.com>
 * Date: 2016-12-23 09:50:35
 */
namespace FurthestWorld\Validator\Src\Rules;

use FurthestWorld\Validator\Src\Code\CodeService;

class ArrayRules {

    /**
     * @node_name 数组简单递归合并，相同的覆盖，不同的合并
     * @return array|void
     */
    public static function array_merge_recursive_simple() {

        if (func_num_args() < 2) {
            trigger_error(__FUNCTION__ . ' needs two or more array arguments', E_USER_WARNING);
            return;
        }
        $arrays = func_get_args();
        $merged = array();
        while ($arrays) {
            $array = array_shift($arrays);
            if (!is_array($array)) {
                trigger_error(__FUNCTION__ . ' encountered a non array argument', E_USER_WARNING);
                return;
            }
            if (!$array) {
                continue;
            }
            foreach ($array as $key => $value) {
                if (is_array($value) && array_key_exists($key, $merged) && is_array($merged[$key])) {
                    $merged[$key] = call_user_func(__FUNCTION__, $merged[$key], $value);
                } else {
                    $merged[$key] = $value;
                }
            }
        }
        return $merged;
    }


    /**
     * @node_name 多维数组转换成一维数组
     * @param array  $multi_arr
     * @param string $exploder
     * @return array
     */
    public static function multiArray2SingleArray($multi_arr = [], $exploder = '.') {
        static $prefix_keys = [];
        static $single_arr = [];
        if (is_array($multi_arr)) {
            foreach ($multi_arr as $key => $value) {
                if (is_array($value)) {
                    array_push($prefix_keys, $key);
                    self::multiArray2SingleArray($value, $exploder);
                    array_pop($prefix_keys);
                } else {
                    array_push($prefix_keys, $key);
                    $single_arr[implode($exploder, $prefix_keys)] = $value;
                    array_pop($prefix_keys);
                }
            }
        }
        return $single_arr;
    }

    /**
     * @node_name 一维数组转换成多维数组
     * @param array  $single_arr
     * @param array  $multi_arr
     * @param string $exploder
     */
    public static function singleArray2multiArray($single_arr = [], &$multi_arr = [], $exploder = '.') {
        if (is_array($single_arr)) {
            foreach ($single_arr as $key => $value) {
                $explode_key = explode($exploder, $key);
                $shift_key   = array_shift($explode_key);
                if (count($explode_key) > 0) {
                    self::singleArray2multiArray([implode($exploder, $explode_key) => $value], $multi_arr[$shift_key], $exploder);
                } else {
                    !is_array($multi_arr) && $multi_arr = [];
                    $multi_arr[$shift_key] = $value;
                }
            }
        }
    }
}