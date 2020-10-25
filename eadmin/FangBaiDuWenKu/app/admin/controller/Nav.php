<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Nav as LogicNav;


/**
 * 前台导航控制器
 */
class Nav extends AdminBase
{
    
    /**
     * 导航逻辑
     */
	
    private static $navLogic = null;
    
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
     
        self::$navLogic = get_sington_object('navLogic', LogicNav::class);
    }

    
    /**
     * 导航列表
     */
    public function navList()
    {
        
        $where = self::$navLogic->getWhere($this->param);
        
        $this->assign('list', self::$navLogic->getNavList($where, true, 'id desc'));
       
       
        return $this->fetch('nav_list');
    }
    
    /**
     * 导航添加
     */
    public function navAdd()
    {
        
        IS_POST && $this->jump(self::$navLogic->navAdd($this->param));
        
        return $this->fetch('nav_add');
    }
    /**
     * 导航编辑
     */
    public function navEdit()
    {
    	$info = self::$navLogic->getNavInfo(['id' => $this->param['id']]);
    	IS_POST && $this->jump(self::$navLogic->navEdit($this->param,$info));
    	
    	
    	$this->assign('info', $info);
    	return $this->fetch('nav_edit');
    }
    /**
     * 导航批量删除
     */
    public function navAlldel($ids = 0)
    {
    
    	$this->jump(self::$navLogic->navAlldel($ids));
    }
    /**
     * 导航删除
     */
    public function navDel($id = 0)
    {
        
        $this->jump(self::$navLogic->navDel(['id' => $id]));
    }
    /**
     * 导航状态更新
     */
    public function navCstatus($id = 0,$status)
    {
        
        $this->jump(self::$navLogic->setNavValue(['id' => $id],'status',$status));
    }
}
