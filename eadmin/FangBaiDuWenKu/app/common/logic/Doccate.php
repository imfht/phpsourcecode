<?php

namespace app\common\logic;

/**
 * 文档分类逻辑
 */
class Doccate extends LogicBase
{
    
    // 文档分类模型
    public static $doccateModel = null;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
        self::$doccateModel = model($this->name);
    }
    
    /**
     * 获取文档分类信息
     */
    public function getDoccateInfo($where = [], $field = true)
    {
        
        return self::$doccateModel->getInfo($where, $field);
    }
    
    /**
     * 获取文档分类列表
     */
    public function getDoccateList($where = [], $field = true, $order = '',$page=0)
    {
        
        return self::$doccateModel->getList($where, $field, $order,$page);
    }
    
    /**
     * 获取文档分类列表搜索条件
     */
    public function getWhere($data = [])
    {
        
        $where = [];
        
        !empty($data['search_data']) && $where['name'] = ['like', '%'.$data['search_data'].'%'];
        !empty($data['pid'])  && $where['pid'] = $data['pid'];
        if (!is_administrator()) {
            
         
        }
        
        return $where;
    }
    
  
    /**
     * 文档分类添加
     */
    public function doccateAdd($data = [])
    {
       
       
    	$validate = validate($this->name);
    	
    	$validate_result = $validate->scene('add')->check($data);
    	
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
        

       
        return self::$doccateModel->setInfo($data) ? [RESULT_SUCCESS, '添加成功'] : [RESULT_ERROR, self::$doccateModel->getError()];
    }
    /**
     * 文档分类编辑
     */
    public function doccateEdit($data = [],$info)
    {
    	$validate = validate($this->name);
    	
    	$validate_result = $validate->scene('edit')->check($data);
    	
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
    	

    
    	return self::$doccateModel->setInfo($data) ? [RESULT_SUCCESS, '编辑成功'] : [RESULT_ERROR, self::$doccateModel->getError()];
    }
    /**
     * 设置文档分类信息
     */
    public function setDoccateValue($where = [], $field = '', $value = '')
    {
       
        return self::$doccateModel->setFieldValue($where, $field, $value) ? [RESULT_SUCCESS, '状态更新成功'] : [RESULT_ERROR, self::$doccateModel->getError()];
    }
    /**
     * 文档分类批量删除
     */
    public function doccateAlldel($ids)
    {
    	

    return self::$doccateModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, self::$doccateModel->getError()];
    }  
    /**
     * 文档分类删除
     */
    public function doccateDel($where = [])
    {
        
      
        
        return self::$doccateModel->deleteInfo($where) ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, self::$doccateModel->getError()];
    }
}
