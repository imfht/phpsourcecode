<?php

namespace app\common\model;

use think\Model;

class User extends Model
{
    protected $autoWriteTimestamp = true;

    public function setPasswordAttr($value)
    {
        return md5($value);
    }
}