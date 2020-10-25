<?php

/**
 * @package Madphp\Http
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp\Http;
use Madphp\Support\Secure;

class Util
{
    /**
     * 获取数组中的单个值或所有值
     * @param $data
     * @param $index
     * @param $default
     * @param $xssClean
     * @return array|string
     */
    public static function fetch($data, $index, $default, $xssClean)
    {
        if ($index === NULL AND !empty($data)) {
            $requestData = array();

            foreach (array_keys($data) as $key) {
                $requestData[$key] = self::fetchFromArray($data, $key, $default, $xssClean);
            }
            return $requestData;
        }

        return self::fetchFromArray($data, $index, $default, $xssClean);
    }

    /**
     * 从数组中获取某个键的值
     * fetch() 方法调用
     * @access  private
     * @param   array
     * @param   string
     * @param   string
     * @param   bool
     * @return  string
     */
    private static function fetchFromArray(&$array, $index = '', $default = '', $xss_clean = FALSE)
    {
        if (!isset($array[$index])) {
            return $default;
        }

        if ($xss_clean === TRUE) {
            return Secure::xssClean($array[$index]);
        }

        return $array[$index];
    }
}