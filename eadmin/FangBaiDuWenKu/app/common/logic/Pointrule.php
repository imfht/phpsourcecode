<?php

namespace app\common\logic;

/**
 *积分规则逻辑
 */
class PointRule extends LogicBase
{
    
    // 会员模型
    public static $pointruleModel = null;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
        self::$pointruleModel = model($this->name);
    }
    
    /**
     * 获取积分规则信息
     */
    public function getPointRuleInfo($where = [], $field = true)
    {
        
        return self::$pointruleModel->getInfo($where, $field);
    }
    
    /**
     * 获取积分规则列表
     */
    public function getPointRuleList($where = [], $field = true, $order = '')
    {
        
        return self::$pointruleModel->getList($where, $field, $order);
    }
    
    /**
     * 获取积分规则列表搜索条件
     */
    public function getWhere($data = [])
    {
        
        $where = [];
        
        !empty($data['search_data']) && $where['name'] = ['like', '%'.$data['search_data'].'%'];
        
        if (!is_administrator()) {
            
         
        }
        
        return $where;
    }
    
  
    /**
     * 积分规则添加
     */
    public function pointRuleAdd($data = [])
    {
    	
    	$validate = validate($this->name);
    	
    	$validate_result = $validate->scene('add')->check($data);
    	
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
    	
       return self::$pointruleModel->setInfo($data) ? [RESULT_SUCCESS, '积分规则添加成功'] : [RESULT_ERROR, self::$pointruleModel->getError()];
    }
    /**
     * 积分规则编辑
     */
    public function pointRuleEdit($data = [],$info)
    {
    	 
    	$validate = validate($this->name);
    	
    	$validate_result = $validate->scene('edit')->check($data);
    	
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
    	
    
    	return self::$pointruleModel->setInfo($data) ? [RESULT_SUCCESS, '积分规则编辑成功'] : [RESULT_ERROR, '1'];
    }
    /**
     * 设置积分规则信息
     */
    public function setPointRuleValue($where = [], $field = '', $value = '')
    {
       
        return self::$pointruleModel->setFieldValue($where, $field, $value) ? [RESULT_SUCCESS, '状态更新成功'] : [RESULT_ERROR, self::$pointruleModel->getError()];
    }
    /**
     * 积分规则批量删除
     */
    public function pointRuleAlldel($ids)
    {
    	

    return self::$pointruleModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '积分规则删除成功'] : [RESULT_ERROR, self::$pointruleModel->getError()];
    }  
    /**
     * 积分规则删除
     */
    public function pointRuleDel($where = [])
    {
        
      
        
        return self::$pointruleModel->deleteInfo($where) ? [RESULT_SUCCESS, '积分规则删除成功'] : [RESULT_ERROR, self::$pointruleModel->getError()];
    }
}
