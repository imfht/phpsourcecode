<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-3-10
 * Time: 下午3:27
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace app\admin\model;

use think\Model;

class RoleConfig extends Model
{

    public function addData($data){
        $data=$this->create($data);
        if(!$data) return false;
        $data['update_time']=time();
        $result=$this->save($data);
        return $result;
    }

    public function saveData($map=array(),$data=array()){
        $data['update_time']=time();
        $result=$this->where($map)->update($data);
        return $result;
    }

    public function deleteData($map){
        $result=$this->where($map)->delete();
        return $result;
    }
} 