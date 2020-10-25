<?php

namespace app\common\model;

use think\Model;

class Dir extends Model
{
    protected $pk = 'dir_id';

    public function setDir($dir_name_id,$up_dir_id = 0)
    {
        $data = [
            'dir_name'=>$dir_name_id,
            'up_dir_id'=>$up_dir_id,
        ];
        $dir_id = $this->where($data)->value('dir_id');
        if($dir_id){
            return $dir_id;
        }else{
            $dir_id = $this->insertGetId($data);
            return $dir_id;
        }
    }
}