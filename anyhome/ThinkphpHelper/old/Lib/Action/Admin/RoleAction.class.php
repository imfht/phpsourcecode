<?php
class RoleAction extends CommonAction {

	public function rolePermission($id = 0)
	{
		
		$Role = D('Role');
		$role = $Role->find($id);
		if ($role['code'] == 'root') {
			$this->success('系统默认该角色（角色ID为1）不受系统限制');
			return;
		}
        $permission = $Role->getPermissionByRid($id);
        $pervolist = array();
        foreach ($permission as $k) {
        	$pervolist[$k['group'].$k['mod'].$k['ac']][] = $k;
        }
        $this->assign('permission', $permission);
        $this->assign('pervolist', $pervolist);
        $Actions = D('Actions');
        $actions= $Actions->getAll();
        
        $aclist = array();
        foreach ($actions as $key) {
        	$vo = array();
        	$v = array();
        	foreach ($key['actions'] as $k) {
        		if ($pervolist[$k['group'].$k['mod'].$k['ac']]) {
        			$k['check'] = 'checked';
        		}
        		$v[] = $k;
        	}
        	$vo = $key;
        	$vo['actions'] = $v;
        	$aclist[] = $vo;
        }
        $this->assign('role', $role);
        $this->assign('actions', $actions);
        $this->assign('aclist', $aclist);
        $this->display();
	}


	public function addRoleNode($rid = 0,$group = '',$mod = '',$ac ='')
	{
		if ($rid == 0||$group == ''||$mod == ''||$ac =='') return ;
		$RolePermission = M('RolePermission');
		$data['rid'] = $rid;
		$data['group'] = $group;
		$data['mod'] = $mod;
		$data['ac'] = $ac;
		$roleP = $RolePermission->where($data)->find();
		if (!$roleP) $RolePermission->add($data);
	}

	public function delRoleNode($rid = 0,$group = '',$mod = '',$ac ='')
	{
		if ($rid == 0||$group == ''||$mod == ''||$ac =='') return ;
		$RolePermission = M('RolePermission');
		$data['rid'] = $rid;
		$data['group'] = $group;
		$data['mod'] = $mod;
		$data['ac'] = $ac;
		$RolePermission->where($data)->delete();
	}
}