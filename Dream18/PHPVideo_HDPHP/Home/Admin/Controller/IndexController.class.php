<?php
/**
 * 「PHP联盟」
 * 后台首页控制器
 * 楚羽幽 <Nmae_Cyu@Foxmail.com>
 */
class IndexController extends AuthController
{
	// 构造函数
	public function __init()
	{
		parent::__init();
	}
	
    // 后台首页
    public function index()
    {
        $this->display();
    }

    // 系统信息视图
    public function welcome()
    {
    	$this->display();
    }
}
