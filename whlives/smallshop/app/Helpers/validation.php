<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/7/3
 * Time: 上午9:39
 */

/**
 * 验证手机号码格式
 * @param $mobile
 * @return bool
 */
function check_mobile($mobile) {
    if (!is_numeric($mobile)) {
        return false;
    }
    if (preg_match("/^1[3456789]{1}\d{9}$/", $mobile)) {
        return true;
    }
    return false;
}

/**
 * 验证价格格式
 * @param $mobile
 * @return bool
 */
function check_price($price) {
    if (preg_match("/(^[-]?[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[-]?[0-9]\.[0-9]([0-9])?$)/", $price)) {
        return true;
    }
    return false;
}