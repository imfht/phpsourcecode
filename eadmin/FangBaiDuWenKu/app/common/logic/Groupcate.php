<?php

namespace app\common\logic;

/**
 * 小组分类逻辑
 */
class Groupcate extends LogicBase
{
    
    // 小组分类模型
    public static $groupcateModel = null;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
        self::$groupcateModel = model($this->name);
    }
    
    /**
     * 获取小组分类信息
     */
    public function getGroupcateInfo($where = [], $field = true)
    {
        
        return self::$groupcateModel->getInfo($where, $field);
    }
    
    /**
     * 获取小组分类列表
     */
    public function getGroupcateList($where = [], $field = true, $order = '',$page=0)
    {
        
        return self::$groupcateModel->getList($where, $field, $order,$page);
    }
    
    /**
     * 获取小组分类列表搜索条件
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
     * 小组分类添加
     */
    public function groupcateAdd($data = [])
    {
       
       
    	$validate = validate($this->name);
    	
    	$validate_result = $validate->scene('add')->check($data);
    	
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
        

       
        return self::$groupcateModel->setInfo($data) ? [RESULT_SUCCESS, '添加成功'] : [RESULT_ERROR, self::$groupcateModel->getError()];
    }
    /**
     * 小组分类编辑
     */
    public function groupcateEdit($data = [],$info)
    {
    	$validate = validate($this->name);
    	
    	$validate_result = $validate->scene('edit')->check($data);
    	
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
    	

    
    	return self::$groupcateModel->setInfo($data) ? [RESULT_SUCCESS, '编辑成功'] : [RESULT_ERROR, self::$groupcateModel->getError()];
    }
    /**
     * 设置小组分类信息
     */
    public function setGroupcateValue($where = [], $field = '', $value = '')
    {
       
        return self::$groupcateModel->setFieldValue($where, $field, $value) ? [RESULT_SUCCESS, '状态更新成功'] : [RESULT_ERROR, self::$groupcateModel->getError()];
    }
    /**
     * 小组分类批量删除
     */
    public function groupcateAlldel($ids)
    {
    	

    return self::$groupcateModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, self::$groupcateModel->getError()];
    }  
    /**
     * 小组分类删除
     */
    public function groupcateDel($where = [])
    {
        
      
        
        return self::$groupcateModel->deleteInfo($where) ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, self::$groupcateModel->getError()];
    }
}
