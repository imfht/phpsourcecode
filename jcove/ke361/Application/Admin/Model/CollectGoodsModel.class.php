<?php
namespace Admin\Model;
use Think\Model;
class CollectGoodsModel extends Model{
	
    public function info($id=0){
        return $this->where("id='".$id."'")->find();
    }

}