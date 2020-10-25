<?php

namespace app\common\model;

use think\Model;

class AuthGroupAccess extends Model
{
    public function authGroup()
    {
        return $this->belongsTo('authGroup', 'group_id', 'id')->bind('rules');
    }
}