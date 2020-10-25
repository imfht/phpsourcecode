<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2018/2/7
 * Time: 13:48
 */

namespace app\common\library;

class Hash
{
    /**
     * 密码加密
     * @param $password
     * @param int $algo
     * @param $options
     * @return bool|string
     */
    public static function hash($password, $algo = PASSWORD_DEFAULT, $options = [])
    {
        return password_hash($password, $algo, $options);
    }

    /**
     * 校验
     * @param $password
     * @param $hash
     * @return bool
     */
    public static function check($password, $hash)
    {
        return password_verify($password, $hash);
    }
}