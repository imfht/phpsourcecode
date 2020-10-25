<?php
/**
 * Desc: 普通验证规则
 * Created by PhpStorm.
 * User: xuanskyer | <furthestworld@icloud.com>
 * Date: 2016-12-23 09:50:35
 */
namespace FurthestWorld\Validator\Src\Rules;

use FurthestWorld\Validator\Src\Code\CodeService;

class NormalRules {

    /**
     * @node_name 要求参数必须为空
     * @param $param_value
     * @return int
     */
    public static function checkEmpty($param_value) {
        return empty($param_value) ? CodeService::CODE_OK : CodeService::CODE_MUST_EMPTY;
    }


    /**
     * @node_name 要求参数必须为非空
     * @param $param_value
     * @return int
     */
    public static function checkNotEmpty($param_value) {
        return !empty($param_value) ? CodeService::CODE_OK : CodeService::CODE_MUST_NOT_EMPTY;
    }

    public static function checkEq($param_value, $set_value) {
        return self::checkSame($param_value, $set_value);
    }

    /**
     * @node_name 验证是否是布尔型或可转换为布尔型
     * @param $param_value
     * @return array
     */
    public static function checkBoolean($param_value) {
        $acceptable = [true, false, 0, 1, '0', '1'];

        if (is_null($param_value) || in_array($param_value, $acceptable, true)) {
            return CodeService::CODE_OK;
        }
        return CodeService::CODE_INVALID_BOOL;
    }

    /**
     * @node_name 要求参数必须为数字
     * @return int
     */
    public static function checkNumber() {
        $args_num  = func_num_args();
        $args_list = func_get_args();
        if (3 == $args_num) {
            if ($args_list[0] < $args_list[1]) {
                return CodeService::CODE_STRING_INVALID_MIN;
            }
            if ($args_list[0] > $args_list[2]) {
                return CodeService::CODE_STRING_INVALID_MAX;
            }
        } elseif (2 == $args_num) {
            if ($args_list[0] < $args_list[1]) {
                return CodeService::CODE_STRING_INVALID_MIN;
            }
        } elseif (1 == $args_num) {
            return is_numeric($args_list[0]) ? CodeService::CODE_OK : CodeService::CODE_MUST_NUMBER;
        }
        return CodeService::CODE_OK;
    }

    /**
     * @node_name 要求参数必须为大于0的数字
     * @param $param_value
     * @return int
     */
    public static function checkNumberGt0($param_value) {
        return (is_numeric($param_value) && $param_value > 0) ? CodeService::CODE_OK : CodeService::CODE_MUST_NUMBER_GT0;
    }

    /**
     * @node_name 参数必须为字符串
     * @param     $param_value
     * @param int $min_length
     * @param int $max_length
     * @return int
     */
    public static function checkString($param_value, $min_length = 0, $max_length = 255) {
        if (!is_string($param_value)) {
            return CodeService::CODE_MUST_STRING;
        } elseif (strlen($param_value) < $min_length) {
            return CodeService::CODE_STRING_INVALID_MIN;
        } elseif (strlen($param_value) > $max_length) {
            return CodeService::CODE_STRING_INVALID_MAX;
        } else {
            return CodeService::CODE_OK;
        }
    }

    /**
     * @node_name 参数必须为数组
     * @param $param_value
     * @return int
     */
    public static function checkArray($param_value) {

        return is_array($param_value) ? CodeService::CODE_OK : CodeService::CODE_MUST_ARRAY;
    }

    /**
     * @node_name 验证是否为字母
     * @param        $param_value
     * @param string $alpha_type    空：不区分大小写，lower-全小写，upper-全大写
     * @return bool
     */
    public static function checkAlpha($param_value, $alpha_type = '') {
        if(!is_string($param_value)){
            return CodeService::CODE_INVALID_ALPHA;
        }
        switch($alpha_type){
            case 'upper':
                return preg_match('/^[A-Z]+$/u', $param_value) ? CodeService::CODE_OK : CodeService::CODE_INVALID_ALPHA_UPPER;
                break;
            case 'lower':
                return preg_match('/^[a-z]+$/u', $param_value) ? CodeService::CODE_OK : CodeService::CODE_INVALID_ALPHA_LOWER;
                break;
            default:
                return preg_match('/^[a-zA-Z]+$/u', $param_value) ? CodeService::CODE_OK : CodeService::CODE_INVALID_ALPHA;
                break;
        }
    }

    /**
     * @node_name 字段值仅允许字母、数字
     * @param $param_value
     * @return array
     */
    public static function checkAlphaNum($param_value) {
        if(!is_string($param_value) && !is_numeric($param_value)){
            return CodeService::CODE_INVALID_ALPHA_NUM;
        }

        return preg_match('/^[0-9a-zA-Z]+$/u', $param_value) ? CodeService::CODE_OK : CodeService::CODE_INVALID_ALPHA_NUM;

    }

    /**
     * @node_name 字段值仅允许字母、数字、破折号（-）以及底线（_）
     * @param $param_value
     * @return array
     */
    public static function checkAlphaDash($param_value) {
        if(!is_string($param_value) && !is_numeric($param_value)){
            return CodeService::CODE_INVALID_ALPHA_DASH;
        }

        return preg_match('/^[0-9a-zA-Z-_]+$/u', $param_value) ? CodeService::CODE_OK : CodeService::CODE_INVALID_ALPHA_DASH;

    }

    /**
     * @node_name 验证是否相等
     * @param $param_value
     * @param $compare_value
     * @return array
     */
    public static function checkSame($param_value, $compare_value) {
        if ($param_value == $compare_value) {
            return CodeService::CODE_OK;
        }
        return CodeService::CODE_NOT_SAME;
    }

    /**
     * @node_name 验证是否是合法的日期
     * @param        $param_value
     * @param string $check_format
     * @return array
     */
    public static function checkValidDate($param_value, $check_format = '') {
        if (empty($check_format)) {
            return strtotime($param_value) ? CodeService::CODE_OK : CodeService::CODE_INVALID_DATE;
        }
        $res = date_parse_from_format($check_format, $param_value);
        if (0 == $res['warning_count'] && 0 == $res['error_count']) {
            return CodeService::CODE_OK;
        }
        return CodeService::CODE_INVALID_DATE;
    }

    /**
     * @node_name 验证是否合法邮箱
     * @param $param_value
     * @return array
     */
    public static function checkEmail($param_value) {
        if (!filter_var($param_value, FILTER_VALIDATE_EMAIL)) {
            return CodeService::CODE_INVALID_EMAIL;
        }
        return CodeService::CODE_OK;
    }

    /**
     * @node_name 验证是否合法json
     * @param $param_value
     * @return array
     */
    public static function checkJson($param_value) {
        if (!is_scalar($param_value) && !method_exists($param_value, '__toString')) {
            return CodeService::CODE_INVALID_JSON;
        }

        json_decode($param_value);
        return json_last_error() === JSON_ERROR_NONE ? CodeService::CODE_OK : CodeService::CODE_INVALID_JSON;
    }

    /**
     * @node_name 验证是否合法IP
     * @param $param_value
     * @return array
     */
    public static function checkIp($param_value) {
        if (!filter_var($param_value, FILTER_VALIDATE_IP)) {
            return CodeService::CODE_INVALID_IP;
        }
        return CodeService::CODE_OK;
    }

    /**
     * @node_name 验证是否合法IP
     * @param $param_value
     * @return array
     */
    public static function checkIpV6($param_value) {
        if (!filter_var($param_value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return CodeService::CODE_INVALID_IPV6;
        }
        return CodeService::CODE_OK;
    }

    /**
     * @node_name 验证是否在列表中
     * @return array
     */
    public static function checkIn() {
        $args_list   = func_get_args();
        $param_value = array_shift($args_list);
        if (!in_array($param_value, $args_list)) {
            return CodeService::CODE_NOT_IN_LIST;
        }
        return CodeService::CODE_OK;
    }

    /**
     * @node_name 验证是否不在列表中
     * @return array
     */
    public static function checkNotIn() {
        $args_list   = func_get_args();
        $param_value = array_shift($args_list);
        if (in_array($param_value, $args_list)) {
            return CodeService::CODE_NOT_IN_LIST;
        }
        return CodeService::CODE_OK;
    }

    /**
     * @node_name 验证是否匹配指定的正则
     * @param $param_value
     * @param $pattern
     * @return array
     */
    public static function checkRegex($param_value, $pattern) {
        if (!preg_match($pattern, $param_value)) {
            return CodeService::CODE_INVALID_REGEX_MATCH;
        }
        return CodeService::CODE_OK;
    }

    /**
     * @node_name 验证是否合法URL
     * @param $param_value
     * @return array
     */
    public static function checkUrl($param_value) {
        if (!filter_var($param_value, FILTER_VALIDATE_URL)) {
            return CodeService::CODE_INVALID_URL;
        }
        return CodeService::CODE_OK;
    }

    /**
     * @node_name 格式化域名
     * @param $param_value
     * @return string
     */
    public static function formatExtendDomain($param_value) {
        return "http://{$param_value}";
    }
}