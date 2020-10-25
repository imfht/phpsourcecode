<?php
namespace Admin\Model;
use Think\Model;


class CateModel extends CommonModel {
    public $_validate	=	array(
        array('name','require','分类名必须'),
        array('name','','分类名已经存在',0,'unique',3),
        );

    public $_auto		=	array(
       
        );
    public function _after_find(&$result,$options) {
    	$result['path']=getThumbImageById($result['img']);
    	
    }
    
    public function _after_select(&$result,$options){
    	foreach($result as &$record){
    		$this->_after_find($record,$options);
    	}
    }
    
    function getlist() {
 	
 	  $data= $this->where('status'==1)->select();
      return $data;
    }
    public function get_spid($pid) {
        if (!$pid) {
            return 0; 
        }
        $pspid = $this->where(array('id'=>$pid))->getField('spid');
        if ($pspid) {
            $spid = $pspid . $pid . '|';
        } else {
            $spid = $pid . '|';
        }
        return $spid;
    }
 /**
     * 获取分类下面的所有子分类的ID集合
     * 
     * @param int $id
     * @param bool $with_self
     * @return array $array 
     */
    public function get_child_ids($id, $with_self=false) {
        $spid = $this->where(array('id'=>$id))->getField('spid');
        $spid = $spid ? $spid .= $id .'|' : $id .'|';
        $id_arr = $this->field('id')->where(array('spid'=>array('like', $spid.'%')))->select();
        $array = array();
        foreach ($id_arr as $val) {
            $array[] = $val['id'];
        }
        $with_self && $array[] = $id;
        return $array;
    }
}
?>