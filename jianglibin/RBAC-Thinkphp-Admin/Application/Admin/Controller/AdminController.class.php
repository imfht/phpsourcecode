<?php
namespace Admin\Controller;
use Think\Controller;

/**
 * @author jlb <[<email address>]>
 * @since 2016年12月7日09:57:37 
 */
class AdminController extends PrivilegeController
{
	/**
	 * 后台用户列表
	 * @author jlb <[<email address>]>
	 * @return [type] [description]
	 */
    public function index()
    {
    	$pageSize = 10;
        $p = I('request.p', 1, 'intval');
        $page = getpage(M('Admin')->count(), $pageSize, array());
    	$adminList = M('Admin')->limit($page->firstRow, $page->listRows)->select();
    	$this->assign('adminList',$adminList);
    	$this->assign('page',$page->show());
        $this->display();
    }
    /**
	 * 后台用户添加
	 * @author jlb <[<email address>]>
	 * @return [type] [description]
	 */
    public function add()
    {
    	if ( IS_POST )
    	{
    		$this->requestSubmit();
    	}
    	$this->assign('actionName','后台用户添加');
    	$this->assign('roleList',M('Role')->where("role_id <> " . ($this->role_id == 1 ? 0 : 1))->select());
        $this->display('form');
    }
    /**
	 * 后台用户编辑
	 * @author jlb <[<email address>]>
	 * @return [type] [description]
	 */
    public function edit()
    {
    	if ( IS_POST )
    	{
    		$this->requestSubmit();
    	}
    	$this->assign('actionName','后台用户编辑');
    	$this->assign('adminInfo',M('Admin')->find(I('get.admin_id')));
    	$this->assign('roleList',M('Role')->where("role_id <> " . ($this->role_id == 1 ? 0 : 1))->select());
        $this->display('form');
    }

    /**
     * 处理添加,编辑用户请求
     * @author jlb 
     * @return [type] [description]
     */
    private function requestSubmit()
    {
    	$admin_id = I('post.admin_id');
    	$role_id = I('post.role_id');
    	$uname = I('post.uname');
    	$pword = I('post.pword');
    	//$admin_id存在就是修改,不存在就是添加
    	if ( !$admin_id && !$uname )
    	{
    		$this->error('请填写账号');
    	}
    	if ( !$admin_id && !$pword )
    	{
    		$this->error('请填写密码');
    	}

    	if ( !$admin_id && M('Admin')->where("uname = '{$uname}'")->count() )
    	{
    		$this->error('账号已经存在,请换一个!');
    	}

    	$data = [
    		'uname' => $uname,
    		'role_id' => $role_id,
    	];

    	if ( $admin_id )
    	{
    		unset($data['uname']);
    		if ( $pword )
    		{
    			$data['pword'] = encrypt($pword);
    		}

    		M('Admin')->where("admin_id = $admin_id")->save($data);
    	}
    	else 
    	{
			$data['pword'] = encrypt($pword);
			$data['create_time'] = time();
    		M('Admin')->add($data);
    	}

		$this->success('操作成功!');
		exit;
    }
    /**
	 * 删除后台人员
	 * @author jlb
	 * @since 2016年12月7日15:00:09 
	 */
	public function del()
	{
		$admin_id = I('get.admin_id',0,'intval');
		if ( !$admin_id ) 
		{
			$this->error('非法请求');
		}
		if ( $admin_id === 1 ) 
		{
			$this->error('不允许删除超级管理员');
		}
		M('Role')->delete($admin_id);
		$this->success('删除成功');
	}
}