<?php
namespace app\articles\model;

use think\Model;

class ArticlesDetail extends Model{

    public function editData($data)
    {
        $d = $this->get($data['articles_id']);
        if($d){
            $res=$this->save($data,['articles_id'=>$data['articles_id']]);
        }else{
            $res=$this->save($data);
        }
        return $res;
    }

    public function getDataById($id)
    {   
        $map['articles_id'] = $id;
        $res=$this->get($map);
        return $res;
    }

}