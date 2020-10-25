<?php
class MemberModel extends CommonModel
{
    //根据MID获取相应的权限
    public function getPermissionByMid($mid = 0)
    {
    	$MemberRole = D('MemberRole');
    	$map['mid'] = $mid;
    	$vo = $MemberRole->where($map)->find();
    	if (!$$vo) return;
    	$perm = $MemberRole->getPermissionByRid($vo['rid']);
    	return $perm;
    }

    //根据MID获取相应的角色信息
    public function getRoleByMid($mid = 0)
    {
    	$map['map'] = $mid;
    	$vo = $this->where($map)->find();
    	return $vo;
    }

    //获取用户的所有信息  包括权限等
    public function getFullInfo($mid = 0)
    {
    	$vo = $this->find($mid);
    	$vo['Permission'] = $this->getPermissionByMid($mid);
    	$vo['Role'] = $this->getRoleByMid($mid);
    	return $vo;
    }
}
?>