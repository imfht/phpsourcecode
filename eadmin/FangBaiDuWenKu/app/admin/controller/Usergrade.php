<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Usergrade as LogicUsergrade;


/**
 * 会员等级控制器
 */
class Usergrade extends AdminBase
{
    
    /**
     * 会员逻辑
     */
	
    private static $usergradeLogic = null;
    
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
      
        self::$usergradeLogic = get_sington_object('usergradeLogic', LogicUsergrade::class);
    }

    
    /**
     * 会员列表
     */
    public function usergradeList()
    {
        
        $where = self::$usergradeLogic->getWhere($this->param);
        
        $this->assign('list', self::$usergradeLogic->getUsergradeList($where, true, 'id desc'));
       
       
        return $this->fetch('usergrade_list');
    }
    
    /**
     * 会员添加
     */
    public function usergradeAdd()
    {
        
        IS_POST && $this->jump(self::$usergradeLogic->usergradeAdd($this->param));
        
        return $this->fetch('usergrade_add');
    }
    /**
     * 会员编辑
     */
    public function usergradeEdit()
    {
    	$info = self::$usergradeLogic->getUsergradeInfo(['id' => $this->param['id']]);
    	IS_POST && $this->jump(self::$usergradeLogic->usergradeEdit($this->param,$info));
    	
    	
    	$this->assign('info', $info);
    	return $this->fetch('usergrade_edit');
    }
    /**
     * 会员批量删除
     */
    public function usergradeAlldel($ids = 0)
    {
    
    	$this->jump(self::$usergradeLogic->usergradeAlldel($ids));
    }
    /**
     * 会员删除
     */
    public function usergradeDel($id = 0)
    {
        
        $this->jump(self::$usergradeLogic->usergradeDel(['id' => $id]));
    }
}
