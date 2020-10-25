<?php
class RoleModel extends CommonModel
{
	protected $_validate = array(
	    array('code','该角色编码已存在','',0,'unique',1),
	 );
	//根据RID获取相应的权限
    public function getPermissionByRid($rid = 0)
    {
    	$RolePermission = M('RolePermission');
    	$map['rid'] = $rid;
    	$volsit = $RolePermission->where($map)->select();
    	return $volsit;
    }

    //根据角色ID 组 模块 操作 判断是否存在权限
    public function checkPermissionByRid($rid = 0, $group = 'Admin', $mod = '', $ac = '')
    {
    	$RolePermission = M('RolePermission');
    	$map['rid'] = $rid;
    	$map['group'] = $group;
    	$map['mod'] = $mod;
    	$map['ac'] = $ac;
    	$vo = $RolePermission->where($map)->find();
    	if ($vo) 
    		return true;
    	return false;
    }


    //根据条件获取相应的权限
    public function getPermission($map = '')
    {
    	$RolePermission = M('RolePermission');
    	$volsit = $RolePermission->where($map)->select();
    	return $volsit;
    }

}
?>