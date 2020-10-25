<?php
namespace Home\Model;

use Think\Model;

class DistrictModel extends Model
{
    public function _list($map){
        $order = 'id ASC';
        $data = $this->where($map)->order($order)->select();
        return $data;
    }
    public function info($id){
        $where['id'] = $id;
        return $this->where($where)->find();
    }
}

?>