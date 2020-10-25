<?php

namespace app\common\logic;

/**
 * 会员等级逻辑
 */
class Usergrade extends LogicBase
{
    
    // 会员模型
    public static $usergradeModel = null;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
        self::$usergradeModel = model($this->name);
    }
    
    /**
     * 获取会员等级信息
     */
    public function getUsergradeInfo($where = [], $field = true)
    {
        
        return self::$usergradeModel->getInfo($where, $field);
    }
    
    /**
     * 获取会员等级列表
     */
    public function getUsergradeList($where = [], $field = true, $order = '')
    {
        
        return self::$usergradeModel->getList($where, $field, $order);
    }
    
    /**
     * 获取会员等级列表搜索条件
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
     * 会员等级添加
     */
    public function usergradeAdd($data = [])
    {
       
       
        $validate = validate($this->name);
        
        $validate_result = $validate->scene('add')->check($data);
        
        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
        
        $url = url('usergradeList');
        

       
        return self::$usergradeModel->setInfo($data) ? [RESULT_SUCCESS, '会员等级添加成功', $url] : [RESULT_ERROR, self::$memberModel->getError()];
    }
    /**
     * 会员等级编辑
     */
    public function usergradeEdit($data = [],$info)
    {
    	 
    	 
    	$validate = validate($this->name);
    
    	$validate_result = $validate->scene('edit')->check($data);
    
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
    
    	$url = url('usergradeList');

    
    	return self::$usergradeModel->setInfo($data) ? [RESULT_SUCCESS, '会员等级编辑成功', $url] : [RESULT_ERROR, '1'];
    }
    /**
     * 设置会员信息
     */
    public function setUsergradeValue($where = [], $field = '', $value = '')
    {
       
        return self::$usergradeModel->setFieldValue($where, $field, $value);
    }
    /**
     * 会员批量删除
     */
    public function usergradeAlldel($ids)
    {
    	

    return self::$usergradeModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '会员等级删除成功'] : [RESULT_ERROR, self::$usergradeModel->getError()];
    }  
    /**
     * 会员删除
     */
    public function usergradeDel($where = [])
    {
        
      
        
        return self::$usergradeModel->deleteInfo($where) ? [RESULT_SUCCESS, '会员等级删除成功'] : [RESULT_ERROR, self::$usergradeModel->getError()];
    }
}
