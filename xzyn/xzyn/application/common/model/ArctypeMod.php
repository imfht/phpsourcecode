<?php
namespace app\common\model;

use think\Model;

class ArctypeMod extends Model
{
    public function getStatusTurnAttr($value, $data)
    {
        $turnArr = [0=>'停用', 1=>'在用'];
        return $turnArr[$data['status']];
    }
}