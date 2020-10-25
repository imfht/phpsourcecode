<?php

namespace app\common\logic;

/**
 * 共用逻辑
 */
class Common extends LogicBase
{
    
   
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
       
    }
    public function test($obj,$callback){
    	
    	call_user_func(array($obj, $callback));
    }
    public function getDataValue($name,$where = [], $field = '', $default = null, $force = false)
    {
    
    	return model($name)->getValue($where, $field, $default , $force);
    }
    
    public function getDataColumn($name,$where = [], $field = '', $key = '')
    {
    	 
    	return model($name)->getColumn($where, $field, $key);
    }
    /**
     * 统计数据
     */
    public function getStat($name,$where = [], $stat_type = 'count', $field = 'id')
    {
    
    	return model($name)->stat($wher,$stat_type,$field);
    }
    /**
     * 获取信息
     */
    public function getDataInfo($name,$where = [], $field = true,$join = [])
    {
               return model($name)->getInfo($where, $field,$join);
    }

    /**
     * 获取列表
     */
    public function getDataList($name,$where = [], $field = true, $order = '', $paginate = 0,$join = [], $group = '',$limit='')
    {

    	return model($name)->getList($where, $field, $order,$paginate, $join, $group,$limit);
    }
    
    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {
        
        $where = [];
        
      //  !empty($data['search_data']) && $where['name'] = ['like', '%'.$data['search_data'].'%'];
      //  !empty($data['pid'])  && $where['pid'] = $data['pid'];
        if (!is_administrator()) {
            
         
        }
        
        return $where;
    }
    /**
     * 设置数据信息
     */
    public function setDataValue($name,$where = [], $field = '', $value = '',$info='状态更新成功',$obj='',$callback='')
    {
    	if($result=model($name)->setFieldValue($where, $field, $value)){
    	
    		if($obj){
    			call_user_func(array($obj, $callback),$value,$where);
    		}
    		return [RESULT_SUCCESS, $info];
    		
    	}else{
    		return [RESULT_ERROR, model($name)->getError()];
    	}
    	
    }
    /**
     * 数据添加insert用户循环添加数据
     */
    public function dataInsert($name,$data = [],$isvalidate=true,$info='添加成功',$obj='',$callback='')
    {
    	 
    	 
    	 
    	if($isvalidate){
    
    		$validate = validate($name);
    		 
    		$validate_result = $validate->scene('add')->check($data);
    		 
    		if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
    
    	}
    	if($result=model($name)->addInfo($data)){
    
    		if($obj){
    			call_user_func(array($obj, $callback),$result,$data);
    		}
    
    
    		return [RESULT_SUCCESS, $info];
    
    	}else{
    		return [RESULT_ERROR, model($name)->getError()];
    	}
    
    
    }
    /**
     * 数据添加，单次添加
     */
    public function dataAdd($name,$data = [],$isvalidate=true,$info='添加成功',$obj='',$callback='')
    {
       
    	
    	
       if($isvalidate){
       	
       	$validate = validate($name);
       	 
       	$validate_result = $validate->scene('add')->check($data);
       	 
       	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
       	
       }
    	if($result=model($name)->setInfo($data)){
    		
    		if($obj){
    			call_user_func(array($obj, $callback),$result,$data);
    		}
    		
    		
    		return [RESULT_SUCCESS, $info];
    		
    	}else{
    		return [RESULT_ERROR, model($name)->getError()];
    	}
        
        
    }
    /**
     * 数据编辑
     */
    public function dataEdit($name,$data = [],$isvalidate=true,$info='编辑成功',$obj='',$callback='')
    {
    	if($isvalidate){
    		
    
    	$validate = validate($name);
    	
    	$validate_result = $validate->scene('edit')->check($data);
    	
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
    	
    	}
    	if($result=model($name)->setInfo($data)){
    	
    		if($obj){
    			call_user_func(array($obj, $callback),$result,$data);
    		}
    	
    	
    		return [RESULT_SUCCESS, $info];
    	
    	}else{
    		return [RESULT_ERROR, model($name)->getError()];
    	}
    
    	
    }


    
    /**
     * 删除
     */
    public function dataDel($name,$where = [],$info='删除成功',$is_true = false)
    {
        
        return model($name)->deleteInfo($where,$is_true) ? [RESULT_SUCCESS, $info] : [RESULT_ERROR, model($name)->getError()];
    }
    
    
}
