<?php
namespace app\common\model;

use think\Model;

class Guestbook extends Model
{
    protected $insert  = ['uid'];

    public function user()
    {
        return $this->hasOne('User', 'id', 'uid')->field('id, username, name');
    }

    protected function setUidAttr($value)
    {
        if ($value){
            return $value;
        }else{
            return session('userId');
        }
    }

    public function getStatusTurnAttr($value, $data)
    {
        $turnArr = [0=>'停用', 1=>'在用'];
        return $turnArr[$data['status']];
    }
}