<?php
namespace app\common\model;

use think\Model;

class Comment extends Model
{
    public function master()
    {
        return $this->hasOne('User', 'id', 'mid')->field('username, name');
    }

    public function user()
    {
        return $this->hasOne('User', 'id', 'uid')->field('username, name');
    }

    public function getStatusTurnAttr($value, $data)
    {
        $turnArr = [0=>'停用', 1=>'在用'];
        return $turnArr[$data['status']];
    }
}