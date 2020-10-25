<?php
namespace Admin\Model;
use Think\Model;

class TagModel extends Model{

    public function getSelect(){
        $result=$this->where('status=1')->getField('id,title');
        return $result;
    }

}