<?php

namespace app\common\logic;

/**
 * 文章分类逻辑
 */
class Articlecate extends LogicBase
{
    
    // 文章分类模型
    public static $articlecateModel = null;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
        self::$articlecateModel = model($this->name);
    }
    
    /**
     * 获取文章分类信息
     */
    public function getArticlecateInfo($where = [], $field = true)
    {
        
        return self::$articlecateModel->getInfo($where, $field);
    }
    
    /**
     * 获取文章分类列表
     */
    public function getArticlecateList($where = [], $field = true, $order = '',$page=0)
    {
        
        return self::$articlecateModel->getList($where, $field, $order,$page);
    }
    
    /**
     * 获取文章分类列表搜索条件
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
     * 文章分类添加
     */
    public function articlecateAdd($data = [])
    {
       
       
    	$validate = validate($this->name);
    	
    	$validate_result = $validate->scene('add')->check($data);
    	
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
        

       
        return self::$articlecateModel->setInfo($data) ? [RESULT_SUCCESS, '添加成功'] : [RESULT_ERROR, self::$articlecateModel->getError()];
    }
    /**
     * 文章分类编辑
     */
    public function articlecateEdit($data = [],$info)
    {
    	$validate = validate($this->name);
    	
    	$validate_result = $validate->scene('edit')->check($data);
    	
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
    	

    
    	return self::$articlecateModel->setInfo($data) ? [RESULT_SUCCESS, '编辑成功'] : [RESULT_ERROR, self::$articlecateModel->getError()];
    }
    /**
     * 设置文章分类信息
     */
    public function setArticlecateValue($where = [], $field = '', $value = '')
    {
       
        return self::$articlecateModel->setFieldValue($where, $field, $value) ? [RESULT_SUCCESS, '状态更新成功'] : [RESULT_ERROR, self::$articlecateModel->getError()];
    }
    /**
     * 文章分类批量删除
     */
    public function articlecateAlldel($ids)
    {
    	

    return self::$articlecateModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, self::$articlecateModel->getError()];
    }  
    /**
     * 文章分类删除
     */
    public function articlecateDel($where = [])
    {
        
      
        
        return self::$articlecateModel->deleteInfo($where) ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, self::$articlecateModel->getError()];
    }
}
