<?php
namespace Home\Model;

use Think\Model;
class TagModel extends Model
{
     public function edit(){
         $this->create();
         $res = $this->info($this->data['id']);
         if( is_array($res)){
             $where['id'] = $this->data['id'];
             return $this->where($where)->save();
         }else {
             return $this->add();
         }
     }
     public function info($id){
         if(empty($id) || $id <=0){
             return -2;
         }
         $where['id'] = $id;
         return $this->where($where)->find();
     }
     
}

?>