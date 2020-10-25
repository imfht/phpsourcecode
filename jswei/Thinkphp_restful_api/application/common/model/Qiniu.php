<?php
namespace app\common\model;

use think\Model;

class Qiniu extends Model{

    public function getPicPath($id=0)
    {
        $result = $this->field('id,path')->find($id);
        return $result['path'];
    }

    public function addNew($name,$path,$size,$type,$ext,&$out=0){
        $data = [
            'name'=>$name,
            'path'=>$path,
            'size'=>$size,
            'type'=>$type,
            'ext'=>$ext,
            'create_time'=>time()
        ];
        if(!$this->insert($data)){
            $out = '上传失败';
            return false;
        }
        $out = $this->getLastInsID();
        return true;
    }
}