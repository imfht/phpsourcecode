<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/8
 * Time: 19:52
 */

namespace app\index\model;


use fastwork\Db;

class User
{

    // 单条查询
    public function info($uid)
    {
        return Db::name('user')->where(['uid' => $uid])->find();
    }
}