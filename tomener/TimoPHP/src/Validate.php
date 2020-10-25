<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo;


/**
 * 验证类
 * 
 * Class Validate
 * @package Timo
 */
class Validate
{
    /**
     * 是否日期
     *
     * @param $str
     * @param string $format
     * @return bool
     */
    public static function isDate($str, $format = 'Y-m-d')
    {
        if (date($format, strtotime($str)) == $str) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 是否非空
     *
     * @param $str
     * @return bool
     */
    public static function isNotNull($str)
    {
        $str = trim($str);
        return (empty($str) && $str != '0') ? false : true;
    }

    /**
     * 是否是QQ号
     *
     * @param $str
     * @return bool
     */
    public static function isQQ($str)
    {
        return preg_match('/^[1-9](\d){6,10}$/', $str) > 0 ? true : false;
    }

    /**
     * 是否是邮箱
     *
     * @param $str
     * @return bool
     */
    public static function isEmail($str)
    {
        return preg_match('/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/i', $str) > 0 ? true : false;
    }

    /**
     * 是否是手机号
     *
     * @param $str
     * @return bool
     */
    public static function isMobile($str)
    {
        return preg_match('/^(((13[0-9]{1})|(14[5-9]{1})|(15[0-3]{1})|(15[5-9]{1})|(16[6]{1})|(17[0-8]{1})|(18[0-9]{1})|(19[1|8-9]{1}))+\d{8})$/', $str) > 0 ? true : false;
    }

    /**
     * 是否是座机号
     *
     * @param $str
     * @return bool
     */
    public static function isTel($str)
    {
        return preg_match('/^0\d{2,3}\s{0,1}-\s{0,1}\d{8}$/', $str) > 0 ? true : false;
    }

    /**
     * 验证密码长度
     *
     * @param $str string
     * @return bool
     */
    public static function passwordLen($str)
    {
        $pwd_length = strlen($str);
        return $pwd_length >= 6 && $pwd_length < 32;
    }

    /**
     * 是否是http请求
     *
     * @param $str
     * @return bool
     */
    public static function isHttp($str)
    {
        $preg = "/^(http[s]?:)?(\/{2})?([a-z0-9]+\.)?[a-z0-9]+(\.(com|cn|cc|org|net|com.cn))$/i";
        if (preg_match($preg, $str)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 是否是网址
     *
     * @param $str
     * @return bool
     */
    public static function isUrl($str)
    {
        $preg = "/^(http[s]?:)?(\/{2})?([a-z0-9]+\.)?[a-z0-9]+(\.(com|cn|cc|org|net|com.cn)).*/i";
        if (preg_match($preg, $str)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 是否是用户名
     *
     * @param $str
     * @return bool
     */
    public static function isUserName($str)
    {
        $str = htmlspecialchars_decode($str);
        if (mb_strlen($str, 'utf-8') >= 2 && mb_strlen($str, 'utf-8') <= 32) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 是否为姓名
     *
     * @param $str
     * @return bool
     */
    public static function isName($str)
    {
        return preg_match('/^[\x{4e00}-\x{9fa5}]{2,10}$/u', $str) > 0 ? true : false;
    }

    /**
     * 验证子域名
     *
     * @param $str
     * @return bool
     */
    public static function isSubDomain($str)
    {
        return preg_match("/^[a-z0-9\-]{3,16}$/", $str) > 0 ? true : false;
    }

    /**
     * 是否是身份证号
     *
     * @param $str
     * @return bool
     */
    public static function isIdentity($str)
    {
        return preg_match("/^(\d{15}|\d{18})$/", $str) > 0 ? true : false;
    }

    /**
     * 对密码的检查，格式、复杂性
     *
     * @param $password
     * @param array $check_arr
     * @return array
     */
    public static function checkPassword($password, $check_arr = [])
    {
        if (strlen($password) >= 6 && strlen($password) <= 16 && preg_match('/\S+/', $password)) {
            if (preg_match('/^\d+$/', $password)) {
                return ['code' => false, 'msg' => '密码不能为纯数字'];
            }
            foreach ($check_arr as $key => $value) {
                if ($password == $key) {
                    return ['code' => false, 'msg' => '密码不能和' . $value . '一样'];
                    break;
                }
            }
            $arr = [];
            for ($i = 0; $i < mb_strlen($password); $i++) {
                $arr[] = mb_substr($password, $i, 1, 'UTF-8');
            }
            $arr2 = array_unique($arr);
            if (count($arr2) < 3) {
                return ['code' => false, 'msg' => '密码过于简单'];
            } else {
                return ['code' => true, 'msg' => '输入正确'];
            }

        } else {
            return ['code' => false, 'msg' => '密码长度：6-16位，不能为空'];
        }
    }
}
