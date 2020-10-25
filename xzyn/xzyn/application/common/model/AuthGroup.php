<?php
namespace app\common\model;

use think\Model;

class AuthGroup extends Model
{
    public function getStatusTurnAttr($value, $data)
    {
        $turnArr = [0=>'停用', 1=>'在用'];
        return $turnArr[$data['status']];
    }
    public function getModuleTurnAttr($value, $data)
    {
        $turnArr = ['admin'=>'后台管理员', 'member'=>'前台会员'];
        return $turnArr[$data['module']];
    }
}