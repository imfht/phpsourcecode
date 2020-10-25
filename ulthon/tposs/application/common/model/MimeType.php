<?php
namespace app\common\model;

use think\Model;

class MimeType extends Model
{
    public function getId($name)
    {
        $name_id = $this->where(array('type_name'=>$name))->value('type_id');
        if($name_id){
            return $name_id;
        }else{
            $name_id = $this->insertGetId(array('type_name'=>$name));
            return $name_id;
        }
    }
}