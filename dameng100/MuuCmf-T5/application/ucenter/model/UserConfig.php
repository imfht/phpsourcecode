<?php
namespace app\ucenter\model;

use think\Model;

class UserConfig extends Model
{
    public function addData($data=array())
    {
        $res=$this->save($data);
        return $res;
    }

    public function findData($map=array())
    {
        $res=$this->where($map)->find();
        return $res;
    }

    public function saveValue($map=array(),$value='')
    {
        $res=$this->where($map)->setField('value',$value);
        return $res;
    }
} 