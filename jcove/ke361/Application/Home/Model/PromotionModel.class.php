<?php
namespace Home\Model;

use Think\Model;

class PromotionModel extends Model
{
    
    public function info($id){
        $where['id'] = $id;
        return $this->where($where)->find();
    }
    
}

?>