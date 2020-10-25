<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Common as LogicCommon;


/**
 * 积分规则控制器
 */
class Pointrule extends AdminBase
{
    
   // 配置逻辑
    private static $commonLogic = null;
    
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
      
        self::$commonLogic = get_sington_object('commonLogic', LogicCommon::class,'PointRule');
    }

    public function getWhere($data = [])
    {
    
    	$where = [];
    	 
    	$where['status|>=']=0;
    	 
    	if (!is_administrator()) {
    
    		 
    	}
    
    	return $where;
    }
    /**
     * 积分规则列表
     */
    public function pointruleList()
    {
    	$where = $this->getWhere($this->param);
    	
    	$clist = self::$commonLogic->getDataList($where, true, 'id desc');
    	
    	$this->assign('list', $clist['data']);
    	 
    	$this->assign('page', $clist['page']);
       
        $this->assign('controllerlist',parse_config_attr(webconfig('point_type_list')));
        
        $this->assign('scoretypelist',parse_config_attr(webconfig('scoretype_list')));
        
        return $this->fetch('pointrule_list');
    }
    
    /**
     * 积分规则添加
     */
    public function pointruleAdd()
    {
        
        IS_POST && $this->jump(self::$commonLogic->dataAdd($this->param));
        
        $this->assign('controllerlist',parse_config_attr(webconfig('point_type_list')));
        
        $this->assign('scoretypelist',parse_config_attr(webconfig('scoretype_list')));
        
        return $this->fetch('pointrule_add');
    }
    /**
     * 积分规则编辑
     */
    public function pointruleEdit()
    {
    	IS_POST && $this->jump(self::$commonLogic->dataEdit($this->param,['id' => $this->param['id']]));
    	
    	$info = self::$commonLogic->getDataInfo(['id' => $this->param['id']]);
    	
        $this->assign('controllerlist',parse_config_attr(webconfig('point_type_list')));
        
        $this->assign('scoretypelist',parse_config_attr(webconfig('scoretype_list')));
    	$this->assign('info', $info);
    	return $this->fetch('pointrule_edit');
    }
    /**
     * 积分规则批量删除
     */
    public function pointruleAlldel($ids = 0)
    {
    
    	$this->jump(self::$commonLogic->dataDel(['id' => $ids],'删除成功',true));
    }
    /**
     * 积分规则删除
     */
    public function pointruleDel($id = 0)
    {
        
        $this->jump(self::$commonLogic->dataDel(['id' => $id],'删除成功',true));
    }
}
