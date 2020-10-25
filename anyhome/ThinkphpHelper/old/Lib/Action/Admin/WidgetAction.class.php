<?php
class WidgetAction extends Action{
	public $mid,$mrole,$minfo,$marea;
    //用户父级菜单 子级菜单 当前菜单集 当前菜单
    public $parentlMenus,$childMenus,$currentMenus,$currentMenu;
    public $userAera,$userRole; //当前登录用户的所属 区域ID 角色信息 海岩为 47
    public $group, $ac, $mod;
	public function bar($group = '',$mod = '',$id = 0)
	{
		$this->ac = $ac;
        $this->mod = $mod;
        $this->group = $group;
        $this->assign('id',$id);
        $this->assign('group',$group);
        $this->assign('mod',$mod);
        $this->display('Widget/bar');
	}

	public function add($group = '',$mod = '',$ac = '')
	{
		$this->ac = $ac;
        $this->mod = $mod;
        $this->group = $group;
		$url = U($group.'/'.$mod.'/add');
        $this->assign('url',$url);
        $this->assign('lable','新增');
        $this->assign('class','btn');
        $this->assign('ico','fa-plus');
        $this->display('Widget/btn');
	}

	public function edit($group = '',$mod = '',$ac = '')
	{
		# code...
	}

	public function delete($group = '',$mod = '',$ac = '')
	{
		# code...
	}

	public function btn($group = '',$mod = '',$ac = '')
	{
		# code...
	}
}