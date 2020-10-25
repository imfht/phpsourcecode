<?php
namespace app\common\model;

use think\Model;

class Flink extends Model
{
    public function moduleClass()
    {
        return $this->hasOne('ModuleClass', 'id', 'mid')->field('id, title');
    }

    public function getStatusTurnAttr($value, $data)
    {
        $turnArr = [0=>'停用', 1=>'在用'];
        return $turnArr[$data['status']];
    }
}