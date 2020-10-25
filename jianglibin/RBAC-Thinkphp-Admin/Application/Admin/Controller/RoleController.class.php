<?php
namespace Admin\Controller;
use Think\Controller;

/**
 * @author jlb
 * @since 2016年12月7日09:57:46
 */
class RoleController extends PrivilegeController
{
	/**
	 * 角色管理
	 * @author jlb
	 * @since 2016年12月7日15:45:01
	 */
    public function index()
    {
    	$this->assign('roleList',M('Role')->select());
        $this->display();
    }

    /**
	 * 添加角色
	 * @author jlb
	 * @since 2016年12月7日15:45:01
	 */
    public function add()
    {
        if ( IS_POST )
        {
            $this->requestSubmit();
        }
    	$this->assign('actionName','添加角色');
    	$this->display('form');
    }
    /**
	 * 删除角色
	 * @author jlb
	 * @since 2016年12月7日15:00:09 
	 */
	public function del()
	{
		$role_id = I('get.role_id',0,'intval');
		if ( !$role_id ) 
		{
			$this->error('非法请求');
		}
		if ( $role_id === 1 ) 
		{
			$this->error('不允许删除超级管理员');
		} 

		M('Role')->delete($role_id);
		$this->success('删除成功');
	}
    /**
	 * 修改角色
	 * @author jlb
	 * @since 2016年12月7日15:45:01
	 */
    public function edit()
    {
        if ( IS_POST )
        {
            $this->requestSubmit();
        }
    	$this->assign('actionName','修改角色');
    	$this->assign('roleInfo',M('Role')->find(I('get.role_id',0,'intval')));
    	$this->display('form');
    }
    /**
     * 配置权限
     * @author jlb
	 * @since 2016年12月7日15:45:01
     */
    public function privilegeEdit()
    {
    	if ( IS_POST )
    	{
    		$role_id = I('post.role_id',0,'intval');
    		$menuIds = I('post.menuIds');
    		if ( $role_id == 1 ) 
	    	{
				$this->success('此组是超级管理员组,不需要配置权限');
				die;
	    	}
    		$datas = [];
    		foreach ($menuIds as $menu_id) {
    			$data['role_id'] = $role_id;
    			$data['menu_id'] = $menu_id;
    			$datas[] = $data;
    		}
    		M('RoleMenu')->where("role_id = $role_id")->delete();
    		M('RoleMenu')->addAll($datas);
			$this->success('配置成功,重新登录即可生效','',3);
			die;
    	}

    	$role_id = I('get.role_id',0,'intval');

    	if ( $role_id == 1 ) 
    	{
			$this->success('超级管理员,不需要配置权限');
			die;
    	}

    	$menuIds = array_column(M('RoleMenu')->where("role_id = $role_id")->select(), 'menu_id');
		$this->assign('menuList',menuArrayTree(array_tree($this->privilegeList, 'menu_id')));
    	$this->assign('checkedMenuIds',$menuIds);
    	$this->assign('actionName','配置权限');
    	$this->display();
    }

    /**
     * 处理增加,编辑角色的请求
     * @author jlb
     * @return [type] [description]
     */
    private function requestSubmit()
    {
        
    	if ( !IS_POST )
    	{
    		$this->error('非法请求');
    	}

    	$rname = I('post.rname','','trim');
    	$role_id = I('post.role_id','','trim');

    	if ( !$rname ) 
    	{
    		$this->error('请填写角色名称');
    	}

    	if ( $role_id ) 
    	{
    		M("Role")->where("role_id = $role_id")->save(I('post.'));
    	}
    	else 
    	{
    		M("Role")->add(I('post.'));
    	}
		$this->success('操作成功!',U('Role/index'));
        die;
    }
}