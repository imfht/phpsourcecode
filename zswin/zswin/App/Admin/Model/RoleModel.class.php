<?php
namespace Admin\Model;
use Think\Model;

// 角色模型
class RoleModel extends CommonModel {
    public $_validate = array(
        array('name','require','组名必须'),
        array('name','','该组名已经存在！',0,'unique',3),
        );

    public $_auto		=	array(
        array('create_time','time',3,'function'),
        array('update_time','time',3,'function'),
        );
        
        
    function delGroupUser($groupId) {
      
        //删除现有的分组下面的所有用户
        $result =D('role_user')->where(array('role_id'=>$groupId))->delete();
        if($result===false) {
            return false;
        }else {
            return true;
        }
    }
    function delGroupUsers($groupId,$userId) {
      
        //删除选择的所有用户以前所有的分组信息
        $map['user_id']=array('in',$userId);
        $result =D('role_user')->where($map)->delete();
        if($result===false) {
            return false;
        }else {
            return true;
        }
    }
    function setGroupUsers($groupId,$userIdList) {
    	
    	 //用于设置用户到用户组中
    	
        if(empty($userIdList)) {
            return true;
        }
        if(is_string($userIdList)) {
            $userIdList = explode(',',$userIdList);
        }
        array_walk($userIdList, array($this, 'fieldFormat'));
        
        foreach ($userIdList as $key=> $vo){
        	
        	$data[$key]['user_id']=$vo;
        	$data[$key]['role_id']=$groupId;
        	
        }
    	
        $result =D('role_user')->addAll($data);
        if($result===false) {
            return false;
        }else {
            return true;
        }

    }

    protected function fieldFormat(&$value) {//格式化数组中的元素
        if(is_int($value)) {
            $value = intval($value);
        } else if(is_float($value)) {
            $value = floatval($value);
        }else if(is_string($value)) {
            $value = addslashes($value);
        }
        return $value;
    }
	function getGroupAllList($groupId)
	{
		//得到所有的组权限菜单
		$acc=D('access')->where(array('role_id'=>$groupId))->getField('node_id',true);
		
		if(empty($acc)){
			return false;
		}
		
		$map['id']=array('in',$acc);
		$rs = D('Node')->where($map)->field('id,title,name')->select();
		return $rs;
	}
    function delGroupAllNode($groupId)
	{
		//删除原有该分组的授权节点信息
		$map['role_id']=$groupId;
        $result = D('access')->where($map)->delete();
		if($result===false) {
			return false;
		}else {
			return true;
		}
	}
    function setGroupAll($groupId,$actionIdList)
	{
		//设置该分组的授权节点
		if(empty($actionIdList)) {
			return true;
		}
	    
		
        $map['id']=array('in',$actionIdList);
		$nodelist=D('node')->where($map)->field('id,pid,level,name')->select();
	    foreach ($nodelist as $key=> $vo){
        	
        	$data[$key]['node_id']=$vo['id'];
        	$data[$key]['pid']=$vo['pid'];
        	$data[$key]['level']=$vo['level'];
        	$data[$key]['module']=$vo['name'];
        	$data[$key]['role_id']=$groupId;
        	
        }
		$result =D('access')->addAll($data);
        if($result===false) {
			return false;
		}else {
			return true;
		}
	}
	
	
	

    function getGroupUserList($groupId) {
    	//获取当前组的用户列表
        $table = $this->tablePrefix.'role_user';
        $rs = $this->db->query('select b.uid,b.nickname from '.$table.' as a ,'.$this->tablePrefix.'Member as b where a.user_id=b.uid and  a.role_id='.$groupId.' ');
        return $rs;
    }


	
}
?>