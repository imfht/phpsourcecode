<?php

namespace app\common\logic;

/**
 * 导航逻辑
 */
class Nav extends LogicBase
{
    
    // 会员模型
    public static $navModel = null;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
        self::$navModel = model($this->name);
    }
    
    /**
     * 获取导航信息
     */
    public function getNavInfo($where = [], $field = true)
    {
        
        return self::$navModel->getInfo($where, $field);
    }
    
    /**
     * 获取导航列表
     */
    public function getNavList($where = [], $field = true, $order = '')
    {
        
        return self::$navModel->getList($where, $field, $order);
    }
    
    /**
     * 获取导航列表搜索条件
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
     * 导航添加
     */
    public function navAdd($data = [])
    {
       
       
        $validate = validate($this->name);
        
        $validate_result = $validate->scene('add')->check($data);
        
        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
        
        $url = url('usergradeList');
        

       
        return self::$navModel->setInfo($data) ? [RESULT_SUCCESS, '导航添加成功', $url] : [RESULT_ERROR, self::$navModel->getError()];
    }
    /**
     * 导航编辑
     */
    public function navEdit($data = [],$info)
    {
    	 
    	 
    	$validate = validate($this->name);
    
    	$validate_result = $validate->scene('edit')->check($data);
    
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
    
    	$url = url('usergradeList');

    
    	return self::$navModel->setInfo($data) ? [RESULT_SUCCESS, '导航编辑成功', $url] : [RESULT_ERROR, '1'];
    }
    /**
     * 设置导航信息
     */
    public function setNavValue($where = [], $field = '', $value = '')
    {
       
        return self::$navModel->setFieldValue($where, $field, $value) ? [RESULT_SUCCESS, '状态更新成功'] : [RESULT_ERROR, self::$navModel->getError()];
    }
    /**
     * 导航批量删除
     */
    public function navAlldel($ids)
    {
    	

    return self::$navModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '导航删除成功'] : [RESULT_ERROR, self::$navModel->getError()];
    }  
    /**
     * 导航删除
     */
    public function navDel($where = [])
    {
        
      
        
        return self::$navModel->deleteInfo($where) ? [RESULT_SUCCESS, '导航删除成功'] : [RESULT_ERROR, self::$navModel->getError()];
    }
}
