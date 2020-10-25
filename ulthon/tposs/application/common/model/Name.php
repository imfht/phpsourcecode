<?php
namespace app\common\model;

use think\Model;

class Name extends Model
{
    public function getId($name)
    {
        $name_id = $this->where(array('name'=>$name))->value('name_id');
        if($name_id){
            return $name_id;
        }else{
            $name_id = $this->insertGetId(array('name'=>$name));
            return $name_id;
        }
    }
}