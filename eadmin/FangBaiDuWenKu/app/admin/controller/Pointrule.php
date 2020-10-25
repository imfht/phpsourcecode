<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Pointrule as LogicPointRule;
use Qiniu\json_decode;


/**
 * 积分规则控制器
 */
class PointRule extends AdminBase
{
    
    /**
     * 积分规则逻辑
     */
	
    private static $pointruleLogic = null;
    
    /**
     * 构造方法
     */
    public function _initialize()
    {
        
        parent::_initialize();
      
        self::$pointruleLogic = get_sington_object('pointruleLogic', LogicPointRule::class);
    }

   
    /**
     * 积分规则列表
     */
    public function pointruleList()
    {
        
        $where = self::$pointruleLogic->getWhere($this->param);
       
        $this->assign('list', self::$pointruleLogic->getPointRuleList($where, true, 'id desc'));
        $this->assign('controllerlist',parse_config_attr(config('point_type_list')));
        $this->assign('scoretypelist',parse_config_attr(config('scoretype_list')));
        
        return $this->fetch('pointrule_list');
    }
    
    /**
     * 积分规则添加
     */
    public function pointruleAdd()
    {
        
        IS_POST && $this->jump(self::$pointruleLogic->pointRuleAdd($this->param));
        $this->assign('controllerlist',parse_config_attr(config('point_type_list')));
        $this->assign('scoretypelist',parse_config_attr(config('scoretype_list')));
        return $this->fetch('pointrule_add');
    }
    /**
     * 积分规则编辑
     */
    public function pointruleEdit()
    {
    	$info = self::$pointruleLogic->getPointRuleInfo(['id' => $this->param['id']]);
    	IS_POST && $this->jump(self::$pointruleLogic->pointRuleEdit($this->param,$info));
    	$this->assign('scoretypelist',parse_config_attr(config('scoretype_list')));
    	$this->assign('controllerlist',parse_config_attr(config('point_type_list')));
    	$this->assign('info', $info);
    	return $this->fetch('pointrule_edit');
    }
    /**
     * 积分规则批量删除
     */
    public function pointruleAlldel($ids = 0)
    {
    
    	$this->jump(self::$pointruleLogic->pointRuleAlldel($ids));
    }
    /**
     * 积分规则删除
     */
    public function pointruleDel($id = 0)
    {
        
        $this->jump(self::$pointruleLogic->pointRuleDel(['id' => $id]));
    }
}
