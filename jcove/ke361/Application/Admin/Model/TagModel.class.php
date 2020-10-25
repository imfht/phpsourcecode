<?php
namespace Admin\Model;

use Think\Model;
class TagModel extends Model
{
     public function edit(){
         $data = $this->create();
         $field = I('field','');
         if(!empty($field)){
             $data['tag_name'] = I('value','');
         }
         $res = $this->info($this->data['id']);
         if( is_array($res)){
             $where['id'] = $this->data['id'];
             return $this->where($where)->save($data);
         }else {
             $map['tag_name'] = $this->data['tag_name'];
             $r = $this->where($map)->find();
             if(is_array($r)){
                 $this->data['status'] = 1;
                 return $this->where($map)->save($data);
             }
             return $this->add($data);
         }
     }
     public function info($id){
         if(empty($id) || $id <=0){
             $this->error.='无效的id';
             return -2;
         }
         $where['id'] = $id;
         return $this->where($where)->find();
     }
     
}

?>