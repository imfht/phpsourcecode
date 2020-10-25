<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Groupcate as LogicGroupcate;


/**
 * 小组分类控制器
 */
class Groupcate extends AdminBase
{
    
    /**
     * 小组分类逻辑
     */
	
    private static $groupcateLogic = null;
    
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
      
        self::$groupcateLogic = get_sington_object('groupcateLogic', LogicGroupcate::class);
    }

    
    /**
     * 小组分类列表
     */
    public function groupcateList()
    {
        
        $where = self::$groupcateLogic->getWhere($this->param);
        
        $this->assign('list', self::$groupcateLogic->getGroupcateList($where, true, 'id desc'));
       
       
        return $this->fetch('groupcate_list');
    }
    
    /**
     * 小组分类添加
     */
    public function groupcateAdd()
    {
        
        IS_POST && $this->jump(self::$groupcateLogic->groupcateAdd($this->param));
        
        return $this->fetch('groupcate_add');
    }
    /**
     * 小组分类编辑
     */
    public function groupcateEdit()
    {
    	$info = self::$groupcateLogic->getGroupcateInfo(['id' => $this->param['id']]);
    	IS_POST && $this->jump(self::$groupcateLogic->groupcateEdit($this->param,$info));
    	
    	
    	$this->assign('info', $info);
    	return $this->fetch('groupcate_edit');
    }
    /**
     * 小组分类批量删除
     */
    public function groupcateAlldel($ids = 0)
    {
    
    	$this->jump(self::$groupcateLogic->groupcateAlldel($ids));
    }
    /**
     * 小组分类删除
     */
    public function groupcateDel($id = 0)
    {
        
        $this->jump(self::$groupcateLogic->groupcateDel(['id' => $id]));
    }
    /**
     * 导航状态更新
     */
    public function groupcateCstatus($id = 0,$status)
    {
    
    	$this->jump(self::$groupcateLogic->setGroupcateValue(['id' => $id],'status',$status));
    }
}
