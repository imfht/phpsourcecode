<?php
/**
 * 数组工具类
 *
 * @namespace application\core\utils
 * @filename ArrayUtil.php
 * @encoding UTF-8
 * @author zqhong <i@zqhong.com>
 * @link http://www.ibos.com.cn/
 * @copyright Copyright &copy; 2012-2016 IBOS Inc
 * @datetime 2016/10/19 11:59
 */

namespace application\core\utils;


class ArrayUtil
{

    /**
     * @param $array
     * @param $columnName
     * @return mixed
     */
    public static function getColumn($array, $columnName)
    {
        return array_map(function ($element) use ($columnName) {
            return $element[$columnName];
        }, $array);
    }

    /**
     * @param $array
     * @param array $keys
     * @return array
     */
    public static function getValueArrayByArrayAndKey($array, $keys = array())
    {
        $returnArray = array();
        if (empty($keys)){
            return $array;
        }
        foreach ($keys as $key){
            if (isset($array[$key])){
                $returnArray[] = $array[$key];
            }
        }
        return $returnArray;
    }

    /**
     * Retrieves the value of an array element or object property with the given key or property name.
     * If the key does not exist in the array or object, the default value will be returned instead.
     *
     * The key may be specified in a dot format to retrieve the value of a sub-array or the property
     * of an embedded object. In particular, if the key is `x.y.z`, then the returned value would
     * be `$array['x']['y']['z']` or `$array->x->y->z` (if `$array` is an object). If `$array['x']`
     * or `$array->x` is neither an array nor an object, the default value will be returned.
     * Note that if the array already has an element `x.y.z`, then its value will be returned
     * instead of going through the sub-arrays.
     *
     * Below are some usage examples,
     *
     * ~~~
     * // working with array
     * $username = \yii\helpers\ArrayHelper::getValue($_POST, 'username');
     * // working with object
     * $username = \yii\helpers\ArrayHelper::getValue($user, 'username');
     * // working with anonymous function
     * $fullName = \yii\helpers\ArrayHelper::getValue($user, function ($user, $defaultValue) {
     *     return $user->firstName . ' ' . $user->lastName;
     * });
     * // using dot format to retrieve the property of embedded object
     * $street = \yii\helpers\ArrayHelper::getValue($users, 'address.street');
     * ~~~
     *
     * @param array|object $array array or object to extract value from
     * @param string|\Closure $key key name of the array element, or property name of the object,
     * or an anonymous function returning the value. The anonymous function signature should be:
     * `function($array, $defaultValue)`.
     * @param mixed $default the default value to be returned if the specified array key does not exist. Not used when
     * getting value from an object.
     * @return mixed the value of the element if found, default value otherwise
     */
    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }

        if (is_array($array) && array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (is_object($array)) {
            return $array->$key;
        } elseif (is_array($array)) {
            return array_key_exists($key, $array) ? $array[$key] : $default;
        } else {
            return $default;
        }
    }

    /**
     * 二维数组排序
     * @param array $arr 需要排序的二维数组
     * @param string $keys 所根据排序的key
     * @param string $type 排序类型，desc、asc
     * @return array $new_array 排好序的结果
     */
    public static function array_muliSort($arr, $keys, $type = 'desc')
    {
        $key_value = $new_array = array();
        foreach ($arr as $k => $v) {
            $key_value[$k] = $v[$keys];
        }
        if ($type == 'asc') {
            asort($key_value);
        } else {
            arsort($key_value);
        }
        reset($key_value);
        foreach ($key_value as $k => $v) {
            $new_array[$k] = $arr[$k];
        }
        return $new_array;

    }

    /**
     * @param $string
     * @param string $split
     * @return array
     */
    public static function stringToArray($string, $split=',')
    {
        if (is_array($string)){
            return $string;
        }
        return explode($split, $string);
    }

    /**
     * 格式化数组成mysql中IN查询所需要的格式
     * @param $array
     */
    public static function formatArrayForSearchInByArray($array)
    {
        if (!is_array($array)){
            $array = explode(',', $array);
        }
        return '"' . implode('","', $array) . '"';
    }

    /**
     * arrayCHtmlEncode 不确定的数组参数过滤
     * @param $array
     */
    public static function arrayCHtmlEncode(&$value)
    {
        if(is_array($value)) {
            foreach ($value as &$val) {
                self::arrayCHtmlEncode($val);
            }
        } else {
            $value = \CHtml::encode($value);
        }
    }
}
