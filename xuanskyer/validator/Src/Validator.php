<?php
/**
 * Desc: 常规请求参数验证服务
 * Created by PhpStorm.
 * User: xuanskyer | <furthestworld@icloud.com>
 * Date: 2016/12/20 17:46
 */
namespace FurthestWorld\Validator\Src;


use FurthestWorld\Validator\Src\Code\CodeService;
use FurthestWorld\Validator\Src\Rules\NormalRules;

class Validator {

    const OPERATE_TYPE_OR       = '|';  //验证类型：或
    const OPERATE_TYPE_AND      = '#';  //验证类型：与
    const OPERATE_METHOD_PARAMS = ':';  //方法和参数分割符
    const OPERATE_MULTI_PARAMS  = ',';  //多个参数直接分隔符
    const CUSTOM_ERROR_CODE_MIN = 2000; //用户自定义错误码最小值
    static private $errors       = [];
    static private $extend_rules = [];
    static private $check_res    = false;  //验证是否通过

    /**
     * @desc 是否验证通过
     * @return bool
     */
    public static function pass() {
        return self::$check_res;
    }
    /**
     * @desc 是否位验证通过
     * @return bool
     */
    public static function fail() {
        return !self::$check_res;
    }

    public static function getErrors() {
        return self::$errors;
    }

    /**
     * @desc      参数格式化
     * @param array $params
     *
     * @param array $format_rules
     * @example   [
     *     ['name' => 'domain', 'default_value' => ''],
     *     ['name' => 'member_id', 'default_value' => 2016],
     *     ['name' => 'level_id', 'default_value' => 1],
     *     ['name' => 'status', 'default_value' => 1],
     *     ['name' => 'expiry_time', 'default_value' => date('Y-m-d H:i:s', time() + 3600 * 24 * 30)],
     *     ['name' => 'created_at', 'default_value' => date('Y-m-d H:i:s')],
     *     ['name' => 'updated_at', 'default_value' => date('Y-m-d H:i:s')]
     *     ]
     * @return array
     */
    public static function formatParams(&$params = [], $format_rules = []) {
        if (is_array($format_rules) && !empty($format_rules)) {
            $valid_fields = array_keys($format_rules);
            foreach ($params as $key => $val) {
                if (!in_array($key, $valid_fields)) {
                    unset($params[$key]);
                }
            }
            foreach ($format_rules as $field => $rule) {
                if (isset($rule['force_value'])) {
                    $params[$field] = $rule['force_value'];
                    continue;
                }
                if (empty($field)) {
                    continue;
                }
                if (isset($rule['default_value']) && empty($params[$field])) {
                    $params[$field] = $rule['default_value'];
                    continue;
                }
                if (isset($rule['format_rule']) && !empty($rule['format_rule'])) {
                    $explode_method   = explode(self::OPERATE_METHOD_PARAMS, $rule['format_rule'], 2);
                    $method_param     = isset($explode_method[1]) && isset($params[$explode_method[1]])
                        ? $params[$explode_method[1]]
                        : (isset($params[$field]) ? $params[$field] : '');
                    $normal_rules_obj = new NormalRules();
                    if (method_exists($normal_rules_obj, $explode_method[0])) {
                        $params[$field] = call_user_func([get_class($normal_rules_obj), $explode_method[0]], $method_param);
                    } else if (function_exists($explode_method[0])) {
                        $params[$field] = $explode_method[0]($method_param);
                    } else {
                        foreach (self::$extend_rules as $extend_name => $extend_obj) {
                            if (method_exists($extend_obj, $explode_method[0])) {
                                $params[$field] = call_user_func([get_class($extend_obj), $explode_method[0]], $method_param);
                            }
                        }
                    }
                }
            }
        }
        return $params;
    }

    /**
     * @desc 参数验证
     * @param array $params
     * @param array $rules
     * @return array|bool
     */
    public static function validateParams($params = [], $rules = [], $code_info = []) {
        if (!is_array($params) || empty($params)) {
            self::$errors = [CodeService::CODE_INVALID_PARAMS];
            return false;
        }
        if (!is_array($rules) || empty($rules)) {
            self::$errors = [CodeService::CODE_INVALID_RULES];
            return false;
        }
        foreach ($rules as $field => $rule) {
            if (!is_array($rule) || empty($rule)) {
                self::$errors = [CodeService::CODE_INVALID_RULES];
                return false;
            }
            if (!isset($field) || !isset($params[$field])) {
                array_push(self::$errors, [$field => CodeService::CODE_NO_PARAM_NAME]);
                return false;
            } else {
                $res_code = self::checkAndSetCode(
                    $field,
                    isset($params[$field]) ? $params[$field] : '',
                    isset($rule['check_rule']) ? $rule['check_rule'] : ''
                );

                if (CodeService::CODE_OK != $res_code) {
                    if (isset($code_info[$field][0]) && $code_info[$field][0] >= self::CUSTOM_ERROR_CODE_MIN) {
                        self::$errors = [$field => $code_info[$field]];
                    } else {
                        self::$errors = [$field => $res_code];
                    }
                    return false;
                }
            }
        }
        self::$check_res = true;
        return self::$check_res;
    }


    /**
     * @node_name 扩展验证规则库
     * @param $extend_name
     * @param $extend_obj
     */
    public static function extend($extend_name, $extend_obj) {
        if (!empty($extend_name) && is_object($extend_obj) && !isset(self::$extend_rules[$extend_name])) {
            self::$extend_rules[$extend_name] = $extend_obj;
        }
    }


    protected static function checkAndSetCode($param_name = '', $param_value = '', $check_rule = '') {
        $res_code         = CodeService::CODE_FAIL;
        $check_or_methods = self::parseCheckMethods($check_rule);
        foreach ($check_or_methods as $check_and_list) {
            $res_code = self::checkParamsRule($param_name, $param_value, $check_and_list);
            if (CodeService::CODE_OK == $res_code) {
                self::$errors = [];
                return $res_code;
            }
        }

        return $res_code;
    }

    /**
     * @node_name 参数验证方法
     * @param string $param_name 参数名
     * @param string $param_value
     * @param array  $and_methods
     * @return int|mixed
     */
    protected static function checkParamsRule($param_name = '', $param_value = '', $and_methods = []) {
        if (empty($param_name)) {
            return CodeService::CODE_NO_PARAM_NAME;
        }
        if (!is_array($and_methods) || empty($and_methods)) {
            return CodeService::CODE_INVALID_RULES;
        }
        foreach ($and_methods as $method_params) {
            $class_name  = $method_params[0][0];
            $method_name = $method_params[0][1];
            if (!method_exists(new $class_name(), $method_name)) {
                return CodeService::CODE_NO_EXISTED_CHECK_METHOD;
            } else {
                $check_params = isset($method_params[1]) ? $method_params[1] : [];
                array_unshift($check_params, $param_value);
                $res_code = call_user_func_array(
                    [$class_name, $method_name],
                    $check_params
                );
                if (CodeService::CODE_OK !== $res_code) {
                    return $res_code;
                }
            }
        }
        return CodeService::CODE_OK;
    }

    /**
     * @node_name 解析验证规则方法和参数
     * @param string $check_rule
     * @return array
     */
    protected static function parseCheckMethods($check_rule = '') {
        $parsed_methods = [];
        if (!empty($check_rule)) {
            $explode_or = explode(self::OPERATE_TYPE_OR, $check_rule);
            foreach ($explode_or as $rule) {
                $methods = self::parseAndCheckMethods($rule);
                !empty($methods) && array_push($parsed_methods, $methods);
            }
        }
        return $parsed_methods;
    }

    /**
     * @node_name 解析与验证方法和参数
     * @param string $check_and_rule
     * @return array
     */
    private static function parseAndCheckMethods($check_and_rule = '') {
        $parsed_methods = [];
        if (!empty($check_and_rule)) {
            $explode_check_type = explode(self::OPERATE_TYPE_AND, $check_and_rule);
            $normal_rule_obj    = new NormalRules();
            foreach ($explode_check_type as $check) {
                if (empty($check)) {
                    continue;
                }
                $check_exp  = explode(self::OPERATE_METHOD_PARAMS, $check, 2);
                $check_name = "check" . ucfirst($check_exp[0]);
                if (method_exists($normal_rule_obj, $check_name)) {
                    $check_params = [];
                    if (isset($check_exp[1]) && !empty($check_exp[1])) {
                        $check_params = explode(self::OPERATE_MULTI_PARAMS, $check_exp[1]);
                    }
                    array_push(
                        $parsed_methods,
                        [
                            [get_class($normal_rule_obj), $check_name],
                            $check_params
                        ]
                    );
                } else {
                    foreach (self::$extend_rules as $extend_name => $extend_obj) {
                        if (method_exists($extend_obj, $check_name)) {
                            $check_params = [];
                            if (isset($check_exp[1]) && !empty($check_exp[1])) {
                                $check_params = explode(self::OPERATE_MULTI_PARAMS, $check_exp[1]);
                            }
                            array_push(
                                $parsed_methods,
                                [
                                    [get_class($extend_obj), $check_name],
                                    $check_params
                                ]
                            );
                        }
                    }
                }
            }
        }
        return $parsed_methods;
    }
}