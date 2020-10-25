<?php
namespace Admin\Controller;

/**
 * @author jlb <[<email address>]>
 * @since 2016年12月6日17:32:09 [<description>]
 */
class MenuController extends PrivilegeController
{

	public function __construct()
	{
		parent::__construct();
		/**
		 * 树状菜单数组
		 */
		$this->assign('menuList',menuArrayTree(array_tree($this->privilegeList, 'menu_id')));
	}
	/**
	 * 菜单列表
	 * @return [type] [description]
	 */
    public function index()
	{
		$this->display();
	}

	/**
	 * 添加菜单
	 * @author jlb
	 * @since 2016年12月7日15:19:18
	 */
	public function add()
	{
		if (IS_POST)
		{
			$this->requestMenu();
		}
		$this->assign('actionName','添加菜单');
		$this->display('form');
	}
	/**
	 * 添加子菜单
	 * @author jlb
	 * @since 2016年12月7日15:19:18
	 */
	public function addChild()
	{
		if (IS_POST)
		{
			$this->requestMenu();
		}
		$this->assign('actionName','添加子菜单');
		$this->display('form');
	}
	/**
	 * 编辑菜单
	 * @author jlb
	 * @since 2016年12月7日15:19:18
	 */
	public function edit()
	{
		if (IS_POST)
		{
			$this->requestMenu();
		}


		$menu_id = I('get.menu_id',0,'intval');
		if ( !$menu_id ) 
		{
			$this->error('非法请求');
		}

		$menuInfo = M('Menu')->find($menu_id);

		if ( !$menuInfo ) 
		{
			$this->error('该菜单已不存在,请重新登录在试!');
		}

		$this->assign('menuInfo',$menuInfo);
		$this->assign('actionName','编辑菜单');
		$this->display('form');
	}

	/**
	 * 删除菜单
	 * @author jlb
	 * @since 2016年12月7日15:00:09 
	 */
	public function del()
	{
		$menu_id = I('get.menu_id',0,'intval');
		if ( !$menu_id ) 
		{
			$this->error('非法请求');
		}

		//判断是否有子菜单
		$hasChild = M('Menu')->where("pid = $menu_id")->count();
		if ( $hasChild )
		{
			$this->error('请先删除子类菜单');
		}

		M('Menu')->delete($menu_id);
		$this->savePrivilege();
		$this->success('删除成功');
	}

	/**
	 * 处理添加,修改菜单业务逻辑
	 * @return [type] [description]
	 */
	private function requestMenu()
	{
		if ( !IS_POST ) 
		{
			$this->error('非法请求');
		}

		$menu_id = I('post.menu_id','','trim');
		$pid = I('post.pid','','trim');
		$name = I('post.name','','trim');
		$controller = I('post.controller','','trim');
		$action = I('post.action','','trim');
		$status = I('post.status',1);
		$power = I('post.power',1);
		$step = I('post.step',0,'intval');

		if ( !$name )
		{
			$this->error('请输入菜单名称');
		}
		if ( $pid != 0 && !$controller )
		{
			$this->error('请输入控制器名称');
		}
		if ( $pid != 0 && !$action )
		{
			$this->error('请输入方法名称');
		}

		$data = I('post.');
		$data['url'] = "{$controller}/{$action}";
		//是修改请求
		if ( $menu_id )
		{
			M('Menu')->where("menu_id = $menu_id")->save($data);
		}
		else //是新增请求 
		{
			M('Menu')->add($data);
		}
		$this->savePrivilege();
		$this->success('操作成功',U('Menu/index'),3);
		exit;
	}
}