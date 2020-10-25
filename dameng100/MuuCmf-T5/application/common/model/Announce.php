<?php
namespace app\common\model;


use think\Model;

class Announce extends Model{

    public function getListPage($map,$order='create_time desc',$r=10)
    {
        $totalCount=$this->where($map)->count();
        $list=$this->where($map)->order($order)->paginate($r);

        return array($list,$totalCount);
    }

    public function addData($data)
    {
        $res=$this->save($data);
        return $res;
    }

    public function saveData($data)
    {
        $res=$this->save($data,$data['id']);
        cache('Announce_detail_'.$data['id'],null);
        return $res;
    }

    public function getDataById($id)
    {
        $data=cache('Announce_detail_'.$id);
        if($data===false){
            $data=$this->find($id);
            cache('Announce_detail_'.$id,$data);
        }
        return $data;
    }


} 