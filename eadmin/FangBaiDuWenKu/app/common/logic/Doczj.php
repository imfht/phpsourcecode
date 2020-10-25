<?php

namespace app\common\logic;

/**
 * 文档专辑逻辑
 */
class Doczj extends LogicBase
{
    
    // 文档专辑模型
    public static $doczjModel = null;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
        self::$doczjModel = model($this->name);
    }
    
    /**
     * 获取文档专辑信息
     */
    public function getDoczjInfo($where = [], $field = true)
    {
        
        return self::$doczjModel->getInfo($where, $field);
    }
    
    /**
     * 获取文档专辑列表
     */
    public function getDoczjList($where = [], $field = true, $order = '')
    {
        
        return self::$doczjModel->getList($where, $field, $order);
    }
    
    /**
     * 获取文档专辑列表搜索条件
     */
    public function getWhere($data = [])
    {
        
        $where = [];
        
        !empty($data['search_data']) && $where['name'] = ['like', '%'.$data['search_data'].'%'];
       // !empty($data['pid'])  && $where['pid'] = $data['pid'];
        if (!is_administrator()) {
            
         
        }
        
        return $where;
    }
    
  
    /**
     * 文档专辑添加
     */
    public function doczjAdd($data = [])
    {
       
       
    	$validate = validate($this->name);
    	
    	$validate_result = $validate->scene('add')->check($data);
    	
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
        

       
        return self::$doczjModel->setInfo($data) ? [RESULT_SUCCESS, '添加成功'] : [RESULT_ERROR, self::$doczjModel->getError()];
    }
    /**
     * 文档专辑编辑
     */
    public function doczjEdit($data = [],$info)
    {
    	$validate = validate($this->name);
    	
    	$validate_result = $validate->scene('edit')->check($data);
    	
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
    	

    
    	return self::$doczjModel->setInfo($data) ? [RESULT_SUCCESS, '编辑成功'] : [RESULT_ERROR, self::$doczjModel->getError()];
    }
    /**
     * 设置文档专辑信息
     */
    public function setDoczjValue($where = [], $field = '', $value = '')
    {
       
        return self::$doczjModel->setFieldValue($where, $field, $value) ? [RESULT_SUCCESS, '状态更新成功'] : [RESULT_ERROR, self::$doczjModel->getError()];
    }
    /**
     * 文档专辑批量删除
     */
    public function doczjAlldel($ids)
    {
    	

    return self::$doczjModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, self::$doczjModel->getError()];
    }  
    /**
     * 文档专辑删除
     */
    public function doczjDel($where = [])
    {
        
      
        
        return self::$doczjModel->deleteInfo($where) ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, self::$doczjModel->getError()];
    }
}
