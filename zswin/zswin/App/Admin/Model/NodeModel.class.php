<?php
namespace Admin\Model;
use Think\Model;

// 节点模型
class NodeModel extends CommonModel {
    protected $_validate	=	array(
        
         
        );

    public function checkNode() {
        $map['name']	 =	 $_POST['name'];
        $map['pid']	=	isset($_POST['pid'])?$_POST['pid']:0;
        $map['status'] = 1;
        if(!empty($_POST['id'])) {
            $map['id']	=	array('neq',$_POST['id']);
        }
        $result	=	$this->where($map)->field('id')->find();
        if($result) {
            return false;
        }else{
            return true;
        }
    }
public function getAllNode() {
	
	
	
	    $map['remark']='';
		$res = $this->where($map)->select();
		
		$all_node = array();
		$sub_node = array();
		$threesub_node = array();
		
		foreach ($res as $v) {
			if ($v['pid'] == '1')
			{$all_node[] = $v;
			
			}
			else
			{$sub_node[$v['pid']][] = $v;}
			 
		}
	
		foreach ($all_node as $k => $v) {
			if (isset($sub_node[$v['id']]))
			{
			$all_node[$k]['sub_node'] = $sub_node[$v['id']];
			
				
			}
			
		}

		$res = $all_node;
		return $res;
	}
}
?>