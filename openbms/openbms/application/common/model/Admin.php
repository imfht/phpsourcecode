<?php

namespace app\common\model;

use think\Model;

class Admin extends Model
{
    protected $autoWriteTimestamp = true;

    public function setPasswordAttr($value)
    {
        return md5($value);
    }

    public function authGroupAccess()
    {
        return $this->belongsTo('authGroupAccess', 'id', 'uid')->bind('group_id');
    }

    public function authGroup()
    {
        return $this->belongsTo('authGroup', 'group_id', 'id')->bind('name');
    }
}