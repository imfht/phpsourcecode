<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/12/30
 * Time: 14:00
 */

namespace app\admin\model;

use think\Model;

class Admin extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime         = 'create_time';
    protected $updateTime         = false;


    /**
     * 密码加密
     * @param $password
     * @return string
     */
    public static function encryptPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function logout()
    {
        return true;
    }
}