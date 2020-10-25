<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Docxs as LogicDocxs;


/**
 * 悬赏控制器
 */
class Docxs extends AdminBase
{
    
    /**
     * 悬赏逻辑
     */
	
    private static $docxsLogic = null;
    
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
     
        self::$docxsLogic = get_sington_object('docxsLogic', LogicDocxs::class);
    }

    
    /**
     * 悬赏列表
     */
    public function docxsList()
    {
        
        $where = self::$docxsLogic->getWhere($this->param);
        
        $this->assign('list', self::$docxsLogic->getDocxsList($where, true, 'm.id desc'));
       
       
        return $this->fetch('docxs_list');
    }
    /**
     * 悬赏批量删除
     */
    public function docxsAlldel($ids = 0)
    {
    
    	$this->jump(self::$docxsLogic->docxsAlldel($ids));
    }
    /**
     * 悬赏删除
     */
    public function docxsDel($id = 0)
    {
        
        $this->jump(self::$docxsLogic->docxsDel(['id' => $id]));
    }
    /**
     * 悬赏批量审核
     */
    public function docxsAllSh($ids = 0)
    {
    
    	$this->jump(self::$docxsLogic->setDocxsAllSh($ids));
    }
    /**
     * 悬赏状态更新
     */
    public function docxsCstatus($id = 0,$status,$field)
    {
    
    	$this->jump(self::$docxsLogic->setDocxsValue(['id' => $id],$field,$status));
    	
    }

}
