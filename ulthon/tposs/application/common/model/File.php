<?php

namespace app\common\model;

use think\Model;

class File extends Model
{
    public function addFile($file_name_id,$up_dir_id,$file_ticket,$mime_type_id)
    {
        $data = [
            'file_name_id'=>$file_name_id,
            'dir_id'=>$up_dir_id,
            'file_ticket'=>$file_ticket,
            'mime_type_id'=>$mime_type_id
        ];
        $file_result = $this->where($data)->find();
        if($file_result){
            return $file_result;
        }else{
            return $this->insert($data);
        }
    }
}