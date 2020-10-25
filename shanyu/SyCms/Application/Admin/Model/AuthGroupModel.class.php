<?php
namespace Admin\Model;
use Think\Model;

class AuthGroupModel extends Model{

    public function getParent($type=1){
        $where['status']=1;
        if($type){
        	$where['type']=2;
        }
        $list=$this->where($where)->getField('id,pid,title');
        $list=\Lib\ArrayTree::listLevel($list);

	    $list_root=array('id'=>0,'title'=>'顶级分组','level'=>0,'mark'=>'');
	    array_unshift($list, $list_root);

        return $list;
    }


}