<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;
use app\common\logic\Common as LogicCommon;

/**
 * 文档控制器
 */
class Pointnote extends AdminBase
{
    
     // 配置逻辑
    private static $commonLogic = null;

    
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
        
        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class,'PointNote');
    }

    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {
    
    	$where = [];
    
    	
    
    	if (!is_administrator()) {
    
    		 
    	}
    
    	return $where;
    }
    /**
     * 文档列表
     */
    public function pointnoteList()
    {
        
        $where = $this->getWhere($this->param);
    	
    	$clist = self::$commonLogic->getDataList($where, true, 'id desc');
    	
    	$this->assign('list', $clist['data']);
    	 
    	$this->assign('page', $clist['page']);
    	
    	$this->assign('controllerlist',parse_config_attr(webconfig('point_type_list')));
    	
    	$this->assign('scoretypelist',parse_config_attr(webconfig('scoretype_list')));
    	
        return $this->fetch('point_note_index');
    }
    
   
    

}
